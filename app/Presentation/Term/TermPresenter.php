<?php declare(strict_types=1);

namespace App\Presentation\Term;

use App\Model\TermFacade;
use App\Presentation\BasePresenter;
use Nette\Application\UI\Form;
use Nette\Database\Row;

final class TermPresenter extends BasePresenter
{
    private ?Row $term = null;

    public function __construct(private TermFacade $terms)
    {
        parent::__construct();
    }

    public function renderDefault(): void
    {
        $rows = $this->terms->getTerms($this->role(), $this->accountCode());
        $now = new \DateTimeImmutable;
        if ($this->role() === 'student') {
            $rows = array_filter($rows, fn($row) => $row->datum_cas > $now);
        }
        $this->template->terms = $rows;
        $this->template->now = $now;
    }

    public function actionCreate(): void
    {
        $this->requireTeacher();
    }

    public function actionEdit(int $id): void
    {
        $this->requireTeacher();
        $this->loadTerm($id);
        $this->checkEditable();
    }

    public function actionDelete(int $id): void
    {
        $this->requireTeacher();
        $this->loadTerm($id);
        $this->checkEditable();
    }

    public function actionDetail(int $id): void
    {
        $this->loadTerm($id);
    }

    public function renderCreate(): void
    {
        $this->template->editing = false;
        $this->setView('form');
    }

    public function renderEdit(): void
    {
        $this->template->editing = true;
        $this->setView('form');
    }

    public function renderDetail(): void
    {
        $this->template->hasOtherTerm = $this->role() === 'student' && !$this->term->zapsan
            && $this->terms->hasOtherEnrollment($this->term, $this->accountCode());
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();
        if ($this->term) {
            $this->template->term = $this->term;
        }
        $this->template->now = new \DateTimeImmutable;
    }

    protected function createComponentTermForm(): Form
    {
        $this->requireTeacher();
        if (!in_array($this->getAction(), ['create', 'edit'], true)) {
            $this->error('Neplatný formulář.', 400);
        }
        $subjects = [];
        foreach ($this->terms->getCourses('pedagog', $this->accountCode()) as $course) {
            $subjects[$course->zkratka_predmetu] = $course->zkratka_predmetu . ' – ' . $course->nazev;
        }
        $form = $this->protectedForm();
        $form->addSelect('zkratka_predmetu', 'Předmět:', $subjects)->setPrompt('Vyberte předmět')->setRequired('Vyberte předmět.');
        $form->addDateTime('datum_cas', 'Datum a čas:')->setRequired('Zadejte platné datum a čas.')
            ->addRule(Form::Range, 'Termín musí být v budoucnosti a rok smí být nejvýše 9999.', [
                new \DateTimeImmutable('+1 minute'), new \DateTimeImmutable('9999-12-31 23:59'),
            ]);
        $form->addSelect('zkratka_mistnosti', 'Místnost:', $this->terms->getRooms())->setPrompt('Vyberte místnost')->setRequired('Vyberte místnost.');
        $form->addText('max_pocet_prihlasenych', 'Max. počet studentů:')->setHtmlType('number')
            ->setRequired('Vyplňte počet studentů.')
            ->addRule(Form::Integer, 'Počet studentů zadejte jako celé číslo.')
            ->addRule(Form::Range, 'Počet musí být mezi 1 a 32767.', [1, 32767]);
        $form->addTextArea('poznamka', 'Poznámka (nejvýše 255 znaků):')
            ->addRule(Form::MaxLength, 'Poznámka může mít nejvýše %d znaků.', 255);
        $form->addSubmit('send', 'Uložit termín');
        if ($this->term) {
            $form->setDefaults($this->term);
            $this->addVersion($form);
        }
        $form->onSuccess[] = $this->termFormSucceeded(...);
        return $form;
    }

    private function termFormSucceeded(Form $form, \stdClass $data): void
    {
        // PHP může například neexistující čas při změně času posunout o hodinu.
        $entered = $form->getHttpData(Form::DataLine, 'datum_cas');
        $start = $data->datum_cas->format('Y-m-d\TH:i');
        if ($entered !== $start && $entered !== $start . ':00') {
            $form['datum_cas']->setValue(null);
            $form['datum_cas']->addError('Zadejte platné datum a čas.');
            return;
        }
        $values = [
            'zkratka_predmetu' => $data->zkratka_predmetu,
            'datum_cas' => $data->datum_cas,
            'zkratka_mistnosti' => $data->zkratka_mistnosti,
            'max_pocet_prihlasenych' => $data->max_pocet_prihlasenych,
            'poznamka' => $data->poznamka,
        ];
        try {
            $this->terms->save($this->term?->id_terminu, $this->accountCode(), $values, $data->version ?? null);
        } catch (\DomainException $e) {
            $form->addError($e->getMessage());
            return;
        }
        $this->flashMessage('Termín byl uložen.', 'success');
        $this->redirect('default');
    }

    protected function createComponentRegistrationForm(): Form
    {
        if ($this->role() !== 'student') {
            $this->error('Zápis je pouze pro studenty.', 403);
        }
        if ($this->getAction() !== 'detail') {
            $this->error('Neplatný formulář.', 400);
        }
        $form = $this->protectedForm();
        $form->addHidden('operation')->setDefaultValue($this->term->zapsan ? 'cancel' : 'enroll')
            ->setRequired('Zápis se nepodařilo odeslat. Zkuste to znovu.')
            ->addRule(Form::Pattern, 'Zápis se nepodařilo odeslat. Zkuste to znovu.', 'cancel|enroll');
        $this->addVersion($form);
        $form->addSubmit('send', $this->term->zapsan ? 'Odhlásit se z termínu' : 'Přihlásit se na termín');
        $form->onSuccess[] = function (Form $form, \stdClass $data): void {
            try {
                if ($data->operation === 'cancel') {
                    $this->terms->cancel($this->term->id_terminu, $this->accountCode(), $data->version);
                } else {
                    $this->terms->enroll($this->term->id_terminu, $this->accountCode(), $data->version);
                }
            } catch (\DomainException $e) {
                $form->addError($e->getMessage());
                return;
            }
            $this->flashMessage($data->operation === 'cancel' ? 'Z termínu jste odhlášeni.' : 'Na termín jste přihlášeni.', 'success');
            $this->redirect('default');
        };
        // Po odmítnutí znovu načteme stav a zobrazíme správné tlačítko.
        $form->onError[] = function (Form $form): void {
            $this->flashMessage($form->getErrors()[0], 'error');
            $this->redirect('this');
        };
        return $form;
    }

    protected function createComponentDeleteForm(): Form
    {
        $this->requireTeacher();
        if ($this->getAction() !== 'delete') {
            $this->error('Neplatný formulář.', 400);
        }
        $form = $this->protectedForm();
        $this->addVersion($form);
        $form->addSubmit('send', 'Smazat termín');
        $form->onSuccess[] = function (\stdClass $data): void {
            try {
                $this->terms->delete($this->term->id_terminu, $this->accountCode(), $data->version);
            } catch (\DomainException $e) {
                $this->flashMessage($e->getMessage(), 'error');
                $this->redirect('this');
            }
            $this->flashMessage('Termín byl smazán.', 'success');
            $this->redirect('default');
        };
        return $form;
    }

    private function addVersion(Form $form): void
    {
        $form->addHidden('version')->setDefaultValue($this->terms->version($this->term))
            ->setRequired('Otevřete aktuální formulář a zkuste to znovu.')
            ->addRule(Form::Pattern, 'Otevřete aktuální formulář a zkuste to znovu.', '[a-f0-9]{64}');
    }

    private function loadTerm(int $id): void
    {
        try {
            $this->term = $this->terms->getTerm($id, $this->role(), $this->accountCode());
        } catch (\DomainException $e) {
            $this->error($e->getMessage(), 404);
        }
    }

    private function checkEditable(): void
    {
        if ($this->term->datum_cas <= new \DateTimeImmutable) {
            $this->flashMessage('Termín už proběhl. Úprava ani smazání nejsou dostupné.', 'error');
            $this->redirect('detail', $this->term->id_terminu);
        }
    }
}

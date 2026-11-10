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

    protected function beforeRender(): void
    {
        parent::beforeRender();
        $this->template->term = $this->term;
        $this->template->now = new \DateTimeImmutable;
    }

    protected function createComponentTermForm(): Form
    {
        $this->requireTeacher();
        $subjects = [];
        foreach ($this->terms->getCourses('pedagog', $this->accountCode()) as $course) {
            $subjects[$course->zkratka_predmetu] = $course->zkratka_predmetu . ' – ' . $course->nazev;
        }
        $form = new Form;
        $form->addProtection();
        $form->addSelect('zkratka_predmetu', 'Předmět:', $subjects)->setPrompt('Vyberte předmět')->setRequired('Vyberte předmět.');
        $form->addDateTime('datum_cas', 'Datum a čas:')->setRequired('Zadejte platné datum a čas.');
        $form->addSelect('zkratka_mistnosti', 'Místnost:', $this->terms->getRooms())->setPrompt('Vyberte místnost')->setRequired('Vyberte místnost.');
        $form->addInteger('max_pocet_prihlasenych', 'Max. počet studentů:')->setRequired('Vyplňte počet studentů.')
            ->addRule(Form::Range, 'Počet musí být mezi 1 a 32767.', [1, 32767]);
        $form->addTextArea('poznamka', 'Poznámka:')->setMaxLength(255);
        $form->addSubmit('send', 'Uložit termín');
        if ($this->term) {
            $form->setDefaults($this->term);
        }
        $form->onSuccess[] = $this->termFormSucceeded(...);
        return $form;
    }

    private function termFormSucceeded(Form $form, \stdClass $data): void
    {
        try {
            $this->terms->save($this->term?->id_terminu, $this->accountCode(), (array) $data);
        } catch (\RuntimeException $e) {
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
        $form = new Form;
        $form->addProtection();
        $form->addHidden('operation')->setDefaultValue($this->term->zapsan ? 'cancel' : 'enroll');
        $form->addSubmit('send', $this->term->zapsan ? 'Odhlásit se z termínu' : 'Přihlásit se na termín');
        $form->onSuccess[] = function (Form $form, \stdClass $data): void {
            try {
                if ($data->operation === 'cancel') {
                    $this->terms->cancel($this->term->id_terminu, $this->accountCode());
                } elseif ($data->operation === 'enroll') {
                    $this->terms->enroll($this->term->id_terminu, $this->accountCode());
                } else {
                    throw new \RuntimeException('Neplatná operace.');
                }
            } catch (\RuntimeException $e) {
                $form->addError($e->getMessage());
                return;
            }
            $this->flashMessage($data->operation === 'cancel' ? 'Z termínu jste odhlášeni.' : 'Na termín jste přihlášeni.', 'success');
            $this->redirect('default');
        };
        return $form;
    }

    protected function createComponentDeleteForm(): Form
    {
        $this->requireTeacher();
        $form = new Form;
        $form->addProtection();
        $form->addSubmit('send', 'Smazat termín');
        $form->onSuccess[] = function (Form $form): void {
            try {
                $this->terms->delete($this->term->id_terminu, $this->accountCode());
            } catch (\RuntimeException $e) {
                $form->addError($e->getMessage());
                return;
            }
            $this->flashMessage('Termín byl smazán.', 'success');
            $this->redirect('default');
        };
        return $form;
    }

    private function loadTerm(int $id): void
    {
        try {
            $this->term = $this->terms->getTerm($id, $this->role(), $this->accountCode());
        } catch (\RuntimeException $e) {
            $this->error($e->getMessage(), 404);
        }
    }

    private function checkEditable(): void
    {
        if ($this->term->datum_cas <= new \DateTimeImmutable) {
            $this->error('Proběhlý termín už nelze upravit ani smazat.', 403);
        }
    }
}

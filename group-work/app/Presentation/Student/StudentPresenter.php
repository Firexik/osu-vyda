<?php declare(strict_types=1);

namespace App\Presentation\Student;

use App\Model\StudentAccounts;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Database\Connection;
use Nette\Security\SimpleIdentity;
use stdClass;

final class StudentPresenter extends Presenter
{
    private SimpleIdentity $student;

    public function __construct(private Connection $database, private StudentAccounts $accounts)
    {
    }

    protected function startup(): void
    {
        parent::startup();
        $this->getHttpResponse()->setHeader('Cache-Control', 'no-store');

        $identity = $this->getUser()->getIdentity();
        if (!$this->getUser()->isLoggedIn() || !($identity instanceof SimpleIdentity)) {
            $this->redirect('Sign:in');
        }

        $this->student = $identity;
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();
        $this->template->student = $this->student->getData();
    }

    public function renderDefault(): void
    {
        $this->template->jobs = $this->database->fetchAll(
            'SELECT name, date_from, date_to, sallary FROM brigady WHERE student_id = ? ORDER BY date_from DESC, id DESC',
            $this->student->getId(),
        );
    }

    protected function createComponentLogoutForm(): Form
    {
        $form = new Form;
        $form->addProtection('Platnost formuláře vypršela. Odešlete jej znovu.');
        $form->addSubmit('send', 'Odhlásit');
        $form->onSuccess[] = function (): void {
            $this->getUser()->logout(true);
            $this->redirect('Sign:in');
        };
        return $form;
    }

    protected function createComponentChangePasswordForm(): Form
    {
        $form = new Form;
        $form->addProtection('Platnost formuláře vypršela. Odešlete jej znovu.');
        $oldPassword = $form->addPassword('oldPassword', 'Staré heslo')
            ->setHtmlAttribute('autocomplete', 'current-password')
            ->setRequired('Zadejte staré heslo.');
        $newPassword = $form->addPassword('newPassword', 'Nové heslo')
            ->setHtmlAttribute('autocomplete', 'new-password')
            ->setRequired('Zadejte nové heslo.')
            ->addRule(Form::NotEqual, 'Nové heslo se musí lišit od starého.', $oldPassword);
        $form->addPassword('confirmPassword', 'Nové heslo znovu')
            ->setHtmlAttribute('autocomplete', 'new-password')
            ->setRequired('Potvrďte nové heslo.')
            ->addRule(Form::Equal, 'Nová hesla se neshodují.', $newPassword);
        $form->addSubmit('send', 'Změnit heslo');
        $form->onSuccess[] = $this->changePassword(...);
        return $form;
    }

    private function changePassword(Form $form, stdClass $values): void
    {
        if (!$this->accounts->changePassword((string) $this->student->getId(), $values->oldPassword, $values->newPassword)) {
            $form->addError('Staré heslo je nesprávné.');
            return;
        }

        $this->flashMessage('Heslo bylo změněno.');
        $this->redirect('Student:default');
    }
}

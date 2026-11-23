<?php declare(strict_types=1);

namespace App\Presentation;

use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{
    protected function startup(): void
    {
        parent::startup();
        if (!$this->getUser()->isLoggedIn() && !in_array($this->getName(), ['Sign', 'Error'], true)) {
            $this->redirect('Sign:in');
        }
    }

    protected function accountCode(): string
    {
        return $this->getUser()->getIdentity()->code;
    }

    protected function role(): string
    {
        return $this->getUser()->isInRole('pedagog') ? 'pedagog' : 'student';
    }

    protected function requireTeacher(): void
    {
        if ($this->role() !== 'pedagog') {
            $this->error('Tato část je pouze pro pedagogy.', 403);
        }
    }

    protected function protectedForm(): Form
    {
        $form = new Form;
        $message = 'Platnost formuláře vypršela. Zkuste to z aktuální stránky.';
        $form->addProtection($message)->setRequired($message);
        return $form;
    }

    protected function createComponentLogoutForm(): Form
    {
        $form = $this->protectedForm();
        $form->addSubmit('send', 'Odhlásit se');
        $form->onSuccess[] = function (): void {
            $this->getUser()->logout(true);
            $this->redirect('Sign:in');
        };
        $form->onError[] = function (Form $form): void {
            $this->flashMessage($form->getErrors()[0], 'error');
            $this->redirect(':Term:default');
        };
        return $form;
    }
}

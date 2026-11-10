<?php declare(strict_types=1);

namespace App\Presentation;

use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{
    protected function startup(): void
    {
        parent::startup();
        if (!$this->getUser()->isLoggedIn() && $this->getName() !== 'Error') {
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

    protected function createComponentLogoutForm(): Form
    {
        $form = new Form;
        $form->addProtection();
        $form->addSubmit('send', 'Odhlásit se');
        $form->onSuccess[] = function (): void {
            $this->getUser()->logout(true);
            $this->redirect('Sign:in');
        };
        return $form;
    }
}

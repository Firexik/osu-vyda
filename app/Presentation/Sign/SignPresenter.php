<?php declare(strict_types=1);

namespace App\Presentation\Sign;

use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Security\AuthenticationException;

final class SignPresenter extends Presenter
{
    public function actionIn(): void
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->redirect('Term:default');
        }
    }

    protected function createComponentSignInForm(): Form
    {
        $form = new Form;
        $form->addProtection();
        $form->addSelect('role', 'Typ účtu:', ['student' => 'Student', 'pedagog' => 'Pedagog']);
        $form->addText('username', 'Přihlašovací jméno:')->setRequired('Vyplňte přihlašovací jméno.')
            ->setHtmlAttribute('autocomplete', 'username');
        $form->addPassword('password', 'Heslo:')->setRequired('Vyplňte heslo.')
            ->setHtmlAttribute('autocomplete', 'current-password');
        $form->addSubmit('send', 'Přihlásit se');
        $form->onSuccess[] = $this->signInFormSucceeded(...);
        return $form;
    }

    private function signInFormSucceeded(Form $form, \stdClass $data): void
    {
        try {
            $this->getUser()->login($data->role . ':' . $data->username, $data->password);
        } catch (AuthenticationException) {
            $form->addError('Nesprávný typ účtu, přihlašovací jméno nebo heslo.');
            return;
        }
        $this->redirect('Term:default');
    }
}

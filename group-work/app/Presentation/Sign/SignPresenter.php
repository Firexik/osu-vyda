<?php declare(strict_types=1);

namespace App\Presentation\Sign;

use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Security\AuthenticationException;
use stdClass;

final class SignPresenter extends Presenter
{
    private const InvalidCredentials = 'Špatný přihlašovací e-mail nebo heslo';

    public function actionIn(): void
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->redirect('Student:default');
        }
    }

    protected function createComponentSignInForm(): Form
    {
        $form = new Form;
        $form->addProtection('Platnost formuláře vypršela. Odešlete jej znovu.');
        $form->addText('email', 'Přihlašovací jméno')
            ->setHtmlAttribute('autocomplete', 'username')
            ->setHtmlAttribute('inputmode', 'email')
            ->setRequired(self::InvalidCredentials);
        $form->addPassword('password', 'Heslo')
            ->setHtmlAttribute('autocomplete', 'current-password')
            ->setRequired(self::InvalidCredentials);
        $form->addSubmit('send', 'Přihlásit');
        $form->onSuccess[] = $this->signIn(...);
        return $form;
    }

    private function signIn(Form $form, stdClass $values): void
    {
        try {
            $this->getUser()->login($values->email, $values->password);
        } catch (AuthenticationException) {
            $form->addError(self::InvalidCredentials);
            return;
        }

        $this->redirect('Student:default');
    }
}

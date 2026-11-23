<?php declare(strict_types=1);

namespace App\Presentation\Sign;

use App\Presentation\BasePresenter;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;

final class SignPresenter extends BasePresenter
{
    public function actionIn(): void
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->redirect('Term:default');
        }
    }

    protected function createComponentSignInForm(): Form
    {
        $form = $this->protectedForm();
        $form->addSelect('role', 'Typ účtu:', ['student' => 'Student', 'pedagog' => 'Pedagog'])
            ->setRequired('Vyberte typ účtu.');
        $form->addText('username', 'Přihlašovací jméno:')->setRequired('Vyplňte přihlašovací jméno.')
            ->addRule(Form::MaxLength, 'Přihlašovací jméno může mít nejvýše %d znaků.', 20)
            ->setHtmlAttribute('autocomplete', 'username');
        $form->addPassword('password', 'Heslo:')->setRequired('Vyplňte heslo.')
            ->addRule(Form::MaxLength, 'Heslo může mít nejvýše %d znaků.', 255)
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

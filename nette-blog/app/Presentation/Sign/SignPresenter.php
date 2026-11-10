<?php declare(strict_types=1);

namespace App\Presentation\Sign;

use Nette;
use Nette\Application\UI\Form;

final class SignPresenter extends Nette\Application\UI\Presenter
{
	protected function createComponentSignInForm(): Form
	{
		$form = new Form;
		$form->addProtection('Platnost formuláře vypršela. Odešlete jej prosím znovu.');
		$form->addText('username', 'Uživatelské jméno:')
			->setHtmlAttribute('autocomplete', 'username')
			->setRequired('Prosím vyplňte své uživatelské jméno.');
		$form->addPassword('password', 'Heslo:')
			->setHtmlAttribute('autocomplete', 'current-password')
			->setRequired('Prosím vyplňte své heslo.');
		$form->addSubmit('send', 'Přihlásit');
		$form->onSuccess[] = $this->signInFormSucceeded(...);

		return $form;
	}

	private function signInFormSucceeded(Form $form, \stdClass $data): void
	{
		try {
			$this->getUser()->login($data->username, $data->password);
		} catch (Nette\Security\AuthenticationException) {
			$form->addError('Nesprávné přihlašovací jméno nebo heslo.');
			return;
		}

		$this->redirect('Home:');
	}

	public function actionOut(): void
	{
		$this->getUser()->logout();
		$this->flashMessage('Odhlášení bylo úspěšné.', 'success');
		$this->redirect('Home:');
	}
}

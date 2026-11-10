<?php declare(strict_types=1);

namespace App\Presentation\Edit;

use Nette;
use Nette\Application\UI\Form;

final class EditPresenter extends Nette\Application\UI\Presenter
{
	public function __construct(
		private Nette\Database\Explorer $database,
	) {
	}

	public function startup(): void
	{
		parent::startup();

		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}
	}

	public function renderEdit(int $id): void
	{
		$post = $this->database->table('posts')->get($id);
		if (!$post) {
			$this->error('Příspěvek nebyl nalezen');
		}

		$this->getComponent('postForm')->setDefaults($post->toArray());
		$this->template->post = $post;
	}

	protected function createComponentPostForm(): Form
	{
		$form = new Form;
		$form->addProtection('Platnost formuláře vypršela. Odešlete jej prosím znovu.');
		$form->addText('title', 'Titulek:')
			->setMaxLength(255)
			->setRequired('Vyplňte titulek příspěvku.');
		$form->addTextArea('content', 'Obsah:')
			->setRequired('Napište obsah příspěvku.');
		$form->addSubmit('send', 'Uložit a publikovat');
		$form->onSuccess[] = $this->postFormSucceeded(...);

		return $form;
	}

	private function postFormSucceeded(array $data): void
	{
		if ($this->getAction() === 'edit') {
			$post = $this->database->table('posts')->get($this->getParameter('id'));
			if (!$post) {
				$this->error('Příspěvek nebyl nalezen');
			}
			$post->update($data);
		} else {
			$post = $this->database->table('posts')->insert($data);
		}

		$this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
		$this->redirect('Post:show', $post->id);
	}
}

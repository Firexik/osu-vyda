<?php declare(strict_types=1);

namespace App\Presentation\Post;

use Nette;
use Nette\Application\UI\Form;

final class PostPresenter extends Nette\Application\UI\Presenter
{
	public function __construct(
		private Nette\Database\Explorer $database,
	) {
	}

	public function renderShow(int $id): void
	{
		$post = $this->database
			->table('posts')
			->get($id);
		if (!$post) {
			$this->error('Stránka nebyla nalezena');
		}

		$this->template->post = $post;
		$this->template->comments = $post->related('comments')->order('created_at, id');
	}

	protected function createComponentCommentForm(): Form
	{
		$form = new Form;
		$form->addProtection('Platnost formuláře vypršela. Odešlete jej prosím znovu.');

		$form->addText('name', 'Jméno:')
			->setMaxLength(255)
			->setRequired('Vyplňte své jméno.');
		$form->addEmail('email', 'E-mail:')
			->setMaxLength(255);
		$form->addTextArea('content', 'Komentář:')
			->setRequired('Napište komentář.');
		$form->addSubmit('send', 'Publikovat komentář');
		$form->onSuccess[] = $this->commentFormSucceeded(...);

		return $form;
	}

	private function commentFormSucceeded(\stdClass $data): void
	{
		$post = $this->database->table('posts')->get($this->getParameter('id'));
		if (!$post) {
			$this->error('Stránka nebyla nalezena');
		}

		$this->database->table('comments')->insert([
			'post_id' => $post->id,
			'name' => $data->name,
			'email' => $data->email,
			'content' => $data->content,
		]);
		$this->flashMessage('Děkuji za komentář', 'success');
		$this->redirect('this');
	}
}

<?php declare(strict_types=1);

namespace App\Model;

use Nette;

final class PostFacade
{
	public function __construct(
		private Nette\Database\Explorer $database,
	) {
	}

	/** @return Nette\Database\Table\Selection<Nette\Database\Table\ActiveRow> */
	public function getPublicArticles(): Nette\Database\Table\Selection
	{
		return $this->database
			->table('posts')
			->where('created_at < ?', new \DateTime)
			->order('created_at DESC');
	}
}

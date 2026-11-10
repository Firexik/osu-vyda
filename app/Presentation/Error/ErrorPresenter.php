<?php declare(strict_types=1);

namespace App\Presentation\Error;

use App\Presentation\BasePresenter;
use Nette\Application\BadRequestException;
use Nette\Application\Attributes\Requires;

#[Requires(forward: true)]
final class ErrorPresenter extends BasePresenter
{
    public function renderDefault(\Throwable $exception): void
    {
        $this->template->status = $exception instanceof BadRequestException ? $exception->getCode() : 500;
        $this->template->message = match ($this->template->status) {
            403 => 'K této stránce nemáte přístup.',
            404 => 'Tato stránka nebo termín neexistuje.',
            default => 'Stránku se nepodařilo načíst. Zkuste to prosím později.',
        };
    }
}

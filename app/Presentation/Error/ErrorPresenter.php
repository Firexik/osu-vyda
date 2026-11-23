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
            400 => 'Požadavek není platný. Vraťte se na přehled a akci zkuste znovu.',
            403 => 'K této stránce nemáte přístup.',
            404 => 'Tato stránka nebo termín už nejsou dostupné.',
            default => 'Stránku se nepodařilo načíst. Zkuste to prosím později.',
        };
    }
}

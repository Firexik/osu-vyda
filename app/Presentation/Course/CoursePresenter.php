<?php declare(strict_types=1);

namespace App\Presentation\Course;

use App\Model\TermFacade;
use App\Presentation\BasePresenter;

final class CoursePresenter extends BasePresenter
{
    public function __construct(private TermFacade $terms)
    {
        parent::__construct();
    }

    public function renderDefault(): void
    {
        $this->template->courses = $this->terms->getCourses($this->role(), $this->accountCode());
    }
}

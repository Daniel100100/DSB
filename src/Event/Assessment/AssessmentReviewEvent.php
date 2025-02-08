<?php
// src/Event/Assessment/AssessmentReviewEvent.php
declare(strict_types=1);

namespace App\Event\Assessment;

use App\Entity\InterestAssessment;
use Symfony\Contracts\EventDispatcher\Event;

class AssessmentReviewEvent extends Event
{
    public const REVIEW_STARTED = 'assessment.review.started';
    public const REVIEW_COMPLETED = 'assessment.review.completed';
    public const REVIEW_REJECTED = 'assessment.review.rejected';

    public function __construct(
        private readonly InterestAssessment $assessment,
        private readonly ?string $comment = null
    ) {
    }

    public function getAssessment(): InterestAssessment
    {
        return $this->assessment;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}
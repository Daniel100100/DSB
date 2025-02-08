<?php
// src/Contracts/Assessment/ReviewServiceInterface.php
declare(strict_types=1);

namespace App\Contracts\Assessment;

use App\DTO\Assessment\AssessmentReviewDTO;
use App\Entity\InterestAssessment;

interface ReviewServiceInterface
{
    public function startReview(InterestAssessment $assessment): void;
    public function processReview(InterestAssessment $assessment, AssessmentReviewDTO $reviewData): void;
}
<?php
// src/Contracts/Assessment/NotificationServiceInterface.php
declare(strict_types=1);

namespace App\Contracts\Assessment;

use App\Entity\InterestAssessment;

interface NotificationServiceInterface
{
    public function sendReviewStartedNotification(InterestAssessment $assessment): void;
    public function sendRejectionNotification(InterestAssessment $assessment, string $comment): void;
}

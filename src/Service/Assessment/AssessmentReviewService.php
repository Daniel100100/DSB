<?php
// src/Service/Assessment/AssessmentReviewService.php
declare(strict_types=1);

namespace App\Service\Assessment;

use App\DTO\Assessment\AssessmentReviewDTO;
use App\Entity\InterestAssessment;
use App\Event\Assessment\AssessmentReviewEvent;
use App\Exception\Assessment\AssessmentReviewException;
use App\Exception\Assessment\AssessmentStateException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AssessmentReviewService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly NotificationService $notificationService,
        private readonly LoggerInterface $logger
    ) {
    }

    public function startReview(InterestAssessment $assessment): void
    {
        try {
            $this->validateAssessmentState($assessment);

            $assessment->setStatus('in Prüfung');
            $this->entityManager->persist($assessment);
            $this->entityManager->flush();

            $event = new AssessmentReviewEvent($assessment);
            $this->eventDispatcher->dispatch($event, AssessmentReviewEvent::REVIEW_STARTED);

            $this->notificationService->sendReviewStartedNotification($assessment);
        } catch (\Exception $e) {
            $this->logger->error('Failed to start review: {message}', [
                'message' => $e->getMessage(),
                'assessmentId' => $assessment->getId(),
                'exception' => $e
            ]);
            throw new AssessmentReviewException('Failed to start review process', 0, $e);
        }
    }

    public function processReview(InterestAssessment $assessment, AssessmentReviewDTO $reviewData): void
    {
        try {
            $assessment->setStatus($reviewData->decision);

            if ($reviewData->decision === 'Prüfung abgelehnt') {
                $event = new AssessmentReviewEvent($assessment, $reviewData->comment);
                $this->eventDispatcher->dispatch($event, AssessmentReviewEvent::REVIEW_REJECTED);
                $this->notificationService->sendRejectionNotification($assessment, $reviewData->comment);
            } else {
                $event = new AssessmentReviewEvent($assessment);
                $this->eventDispatcher->dispatch($event, AssessmentReviewEvent::REVIEW_COMPLETED);
            }

            $this->entityManager->persist($assessment);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->logger->error('Failed to process review: {message}', [
                'message' => $e->getMessage(),
                'assessmentId' => $assessment->getId(),
                'exception' => $e
            ]);
            throw new AssessmentReviewException('Failed to process review', 0, $e);
        }
    }

    private function validateAssessmentState(InterestAssessment $assessment): void
    {
        if (in_array($assessment->getStatus(), ['geprüft', 'in Prüfung'], true)) {
            throw new AssessmentStateException('Assessment already in review or completed');
        }
    }
}
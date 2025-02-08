<?php
// src/EventSubscriber/AssessmentReviewSubscriber.php
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\Assessment\AssessmentReviewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssessmentReviewSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AssessmentReviewEvent::REVIEW_STARTED => 'onReviewStarted',
            AssessmentReviewEvent::REVIEW_COMPLETED => 'onReviewCompleted',
            AssessmentReviewEvent::REVIEW_REJECTED => 'onReviewRejected',
        ];
    }

    public function onReviewStarted(AssessmentReviewEvent $event): void
    {
        $this->logger->info('Assessment review started', [
            'assessmentId' => $event->getAssessment()->getId(),
            'mandantId' => $event->getAssessment()->getMandant()?->getId()
        ]);
    }

    public function onReviewCompleted(AssessmentReviewEvent $event): void
    {
        $this->logger->info('Assessment review completed', [
            'assessmentId' => $event->getAssessment()->getId(),
            'mandantId' => $event->getAssessment()->getMandant()?->getId()
        ]);
    }

    public function onReviewRejected(AssessmentReviewEvent $event): void
    {
        $this->logger->info('Assessment review rejected', [
            'assessmentId' => $event->getAssessment()->getId(),
            'mandantId' => $event->getAssessment()->getMandant()?->getId(),
            'comment' => $event->getComment()
        ]);
    }
}

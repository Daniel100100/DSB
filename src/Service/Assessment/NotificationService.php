<?php
// src/Service/Assessment/NotificationService.php
declare(strict_types=1);

namespace App\Service\Assessment;

use App\Entity\InterestAssessment;
use App\Exception\Assessment\AssessmentReviewException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NotificationService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly LoggerInterface $logger
    ) {
    }

    public function sendReviewStartedNotification(InterestAssessment $assessment): void
    {
        $dsbEmail = $assessment->getMandant()?->getDsbEmail();
        if (!$dsbEmail) {
            $this->logger->warning('No DSB email found', ['assessmentId' => $assessment->getId()]);
            return;
        }

        $this->sendEmail(
            $dsbEmail,
            'Neue Interessenabwägung zur Prüfung',
            'interest_assessment/review_notification_email.html.twig',
            [
                'assessment' => $assessment,
                'reviewUrl' => $this->urlGenerator->generate('interest_assessment_review',
                    ['id' => $assessment->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            ]
        );
    }

    public function sendRejectionNotification(InterestAssessment $assessment, string $comment): void
    {
        $creator = $assessment->getCreator();
        if (!$creator || !$creator->getEmail()) {
            $this->logger->warning('No creator email found', ['assessmentId' => $assessment->getId()]);
            return;
        }

        $this->sendEmail(
            $creator->getEmail(),
            'Interessenabwägung wurde abgelehnt',
            'interest_assessment/rejection_email.html.twig',
            [
                'assessment' => $assessment,
                'comment' => $comment,
                'reviewUrl' => $this->urlGenerator->generate('app_interest_assessment_edit',
                    ['id' => $assessment->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            ]
        );
    }

    private function sendEmail(string $to, string $subject, string $template, array $context): void
    {
        try {
            $email = (new TemplatedEmail())
                ->from('sicher@datenschutzmandat.de')
                ->to($to)
                ->subject($subject)
                ->htmlTemplate($template)
                ->context($context);

            $this->mailer->send($email);
        } catch (\Exception $e) {
            $this->logger->error('Failed to send email: {message}', [
                'message' => $e->getMessage(),
                'to' => $to,
                'template' => $template,
                'exception' => $e
            ]);
            throw new AssessmentReviewException('Failed to send notification email', 0, $e);
        }
    }
}

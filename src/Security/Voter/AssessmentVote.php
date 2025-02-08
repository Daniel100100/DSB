<?php
// src/Security/Voter/AssessmentVoter.php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\InterestAssessment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AssessmentVoter extends Voter
{
    public const REVIEW = 'REVIEW';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof InterestAssessment &&
            in_array($attribute, [self::REVIEW, self::EDIT, self::DELETE], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var InterestAssessment $assessment */
        $assessment = $subject;

        return match($attribute) {
            self::REVIEW => $this->canReview($assessment, $user),
            self::EDIT => $this->canEdit($assessment, $user),
            self::DELETE => $this->canDelete($assessment, $user),
            default => false
        };
    }

    private function canReview(InterestAssessment $assessment, User $user): bool
    {
        if (!$user->getMandant()) {
            return false;
        }

        // DSB can always review
        if (in_array('ROLE_DSB', $user->getRoles(), true)) {
            return true;
        }

        // Creator's mandant check
        return $assessment->getMandant() === $user->getMandant();
    }

    private function canEdit(InterestAssessment $assessment, User $user): bool
    {
        if ($assessment->getStatus() === 'geprüft') {
            return false;
        }

        if (!$user->getMandant()) {
            return false;
        }

        return $assessment->getMandant() === $user->getMandant() &&
            ($assessment->getCreator() === $user || in_array('ROLE_DSB', $user->getRoles(), true));
    }

    private function canDelete(InterestAssessment $assessment, User $user): bool
    {
        if (!$user->getMandant()) {
            return false;
        }

        return $assessment->getMandant() === $user->getMandant() &&
            ($assessment->getCreator() === $user || in_array('ROLE_ADMIN', $user->getRoles(), true));
    }
}
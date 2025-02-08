<?php
// src/DTO/Assessment/AssessmentReviewDTO.php
declare(strict_types=1);

namespace App\DTO\Assessment;

use Symfony\Component\Validator\Constraints as Assert;

class AssessmentReviewDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'Eine Entscheidung muss getroffen werden')]
        #[Assert\Choice(choices: ['geprüft', 'Prüfung abgelehnt'])]
        public readonly string $decision,

        #[Assert\Length(max: 1000, maxMessage: 'Der Kommentar darf nicht länger als {{ limit }} Zeichen sein')]
        public readonly ?string $comment = null
    ) {
    }
}

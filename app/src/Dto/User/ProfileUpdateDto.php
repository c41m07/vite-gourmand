<?php

namespace App\Dto\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ProfileUpdateDto
{
    private UserRepository $userRepository;

    #[Assert\NotBlank(message: 'Le prenom est obligatoire.')]
    #[Assert\Length(max: 100)]
    public string $firstName = '';

    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(max: 100)]
    public string $lastName = '';

    #[Assert\NotBlank(message: 'L\'email est obligatoire.')]
    #[Assert\Email(message: 'L\'email est invalide.')]
    #[Assert\Length(max: 180)]
    public string $email = '';

    #[Assert\Length(max: 20)]
    public ?string $phone = null;

    #[Assert\Length(max: 255)]
    public ?string $postalAddress = null;

    public ?string $currentPassword = null;

    #[Assert\Length(
        min: 10,
        minMessage: 'Le nouveau mot de passe doit contenir au moins 10 caracteres.'
    )]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).+$/',
        message: 'Le nouveau mot de passe doit contenir une minuscule, une majuscule, un chiffre et un caractere special.'
    )]
    public ?string $newPassword = null;

    public ?int $currentUserId = null;

    public function __construct(User $user, UserRepository $userRepository, ?int $currentUserId)
    {
        $this->userRepository = $userRepository;
        $this->currentUserId = $currentUserId;
        $this->firstName = $user->getFirstName() ?? '';
        $this->lastName = $user->getLastName() ?? '';
        $this->email = $user->getEmail() ?? '';
        $this->phone = $user->getPhone();
        $this->postalAddress = $user->getPostalAddress();
    }

    #[Constraints\Callback]
    public function validateEmailUnicity(ExecutionContextInterface $context): void
    {
        $existUser = $this->userRepository->findOneBy(['email' => $this->email]);
        if (!$existUser) {
            return;
        }
        if ($existUser->getId() !== $this->currentUserId) {
            $context->buildViolation('Cet email est deja utilise.')
                ->atPath('email')
                ->addViolation();
        }
    }
}

<?php

namespace App\Application\User\Handler;

use App\Application\User\Exception\InvalidCurrentPasswordException;
use App\Dto\User\ProfileUpdateDto;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProfileUpdateHandler
{
    public function __construct(
        private readonly ValidatorInterface          $validator,
        private readonly EntityManagerInterface      $em,
        private readonly UserRepository              $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    )
    {
    }

    public function handle(
        User    $initialUser,
        User    $userUpdated,
        string  $currentPassword,
        ?string $newPassword
    ): void
    {
        $dto = new ProfileUpdateDto($userUpdated, $this->userRepository, $initialUser->getId());
        $dto->currentPassword = trim($currentPassword);
        $dto->newPassword = ($newPassword !== null && trim($newPassword) !== '') ? trim($newPassword) : null;

//        if ($initialUser->getEmail() !== $profileUpdateDto->email) {
//            $this->sendNewMailConfiramtion($profileUpdateDto->email);
//            // TODO : prendre en compte une adresse mail à modifier ?
//            //      Envoyer un mail de confirmation ?
//            //      Stocker temporairement l'ancienne/nouvelle adresse mail'
//        }

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ValidationFailedException($dto, $errors);
        }

        if ($dto->currentPassword === '' || !$this->passwordHasher->isPasswordValid($initialUser, $dto->currentPassword)) {
            throw new InvalidCurrentPasswordException('Le mot de passe actuel est invalide.');
        }

        $initialUser
            ->setFirstName($dto->firstName)
            ->setLastName($dto->lastName)
            ->setEmail($dto->email)
            ->setPhone($dto->phone)
            ->setPostalAddress($dto->postalAddress);
        if ($dto->newPassword !== null) {
            $initialUser->setPassword(
                $this->passwordHasher->hashPassword($initialUser, $dto->newPassword)
            );
        }

        $this->flusher($initialUser);
    }

    private function flusher(User $user): void
    {
        $user->setUpdatedAt(new DateTime());
        $this->em->flush();
    }


}

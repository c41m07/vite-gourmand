<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class DemoUserFixtures extends Fixture
{
    public const USER_REFERENCE = 'demo-user';
    public const WORKER_REFERENCE = 'demo-worker';
    public const ADMIN_REFERENCE = 'demo-admin';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTime();

        $user = $this->createUser(
            email: 'test@test.fr',
            firstName: 'Client',
            lastName: 'Demo',
            roles: ['ROLE_USER'],
            phone: '0601020304',
            postalAddress: '12 rue des Demoiselles, 33000 Bordeaux',
            createdAt: $now,
            city: 'Quimper',
            postalCode: '29000',
        );
        $manager->persist($user);
        $this->addReference(self::USER_REFERENCE, $user);

        $worker = $this->createUser(
            email: 'worker@test.fr',
            firstName: 'Employe',
            lastName: 'Demo',
            roles: ['ROLE_WORKER'],
            phone: '0602030405',
            postalAddress: '8 quai des Ateliers, 33100 Bordeaux',
            createdAt: $now,
            city: 'Lille',
            postalCode: '33100',
        );
        $manager->persist($worker);
        $this->addReference(self::WORKER_REFERENCE, $worker);

        $admin = $this->createUser(
            email: 'admin@test.fr',
            firstName: 'Admin',
            lastName: 'Demo',
            roles: ['ROLE_ADMIN'],
            phone: '0603040506',
            postalAddress: '1 place de la Victoire, 33000 Bordeaux',
            createdAt: $now,
            city: 'Bordeaux',
            postalCode: '33000',
        );
        $manager->persist($admin);
        $this->addReference(self::ADMIN_REFERENCE, $admin);

        $manager->flush();
    }

    /**
     * @param list<string> $roles
     */
    private function createUser(
        string $email,
        string $firstName,
        string $lastName,
        array $roles,
        string $phone,
        string $postalAddress,
        string $city,
        string $postalCode,
        \DateTime $createdAt,
    ): User {
        $user = (new User())
            ->setEmail($email)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setRoles($roles)
            ->setPhone($phone)
            ->setPostalAddress($postalAddress)
            ->setCreatedAt(clone $createdAt)
            ->setUpdatedAt(clone $createdAt)
            ->setActive(true)
            ->setCity($city)
            ->setPostalCode($postalCode);

        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'Admin1234*/')
        );

        return $user;
    }
}

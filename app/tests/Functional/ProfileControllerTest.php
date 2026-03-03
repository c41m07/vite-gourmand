<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        foreach ($this->entityManager->getRepository(User::class)->findAll() as $user) {
            $this->entityManager->remove($user);
        }
        $this->entityManager->flush();
    }

    public function testProfileAccessIsDeniedForAnonymous(): void
    {
        $this->client->request('GET', '/user/profile');

        self::assertResponseRedirects('/login');
    }

    public function testProfileAccessIsAllowedForRoleUser(): void
    {
        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = static::getContainer()->get('security.user_password_hasher');

        $user = (new User())
            ->setEmail('profile.user@example.com')
            ->setFirstName('Profile')
            ->setLastName('User')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setActive(true)
            ->setRoles(['ROLE_USER']);
        $user->setPassword($passwordHasher->hashPassword($user, 'password123'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->request('GET', '/user/profile?tab=profile');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Mon compte');
        self::assertSelectorTextContains('.account-section-title', 'Informations personnelles');
        self::assertStringContainsString(
            'profile.user@example.com',
            (string) $this->client->getResponse()->getContent()
        );
    }
}


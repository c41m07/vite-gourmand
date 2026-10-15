<?php

namespace App\Tests\Functional\Security;

use App\Entity\User;
use App\Tests\Support\GeneratesTestPasswordsTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginControllerTest extends WebTestCase
{
    use GeneratesTestPasswordsTrait;

    private KernelBrowser $client;
    private string $validPassword;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        $userRepository = $em->getRepository(User::class);

        $em->getConnection()->executeStatement('DELETE FROM customer_order_status_history');
        $em->getConnection()->executeStatement('DELETE FROM customer_order_menu');
        $em->getConnection()->executeStatement('DELETE FROM customer_order');
        $em->getConnection()->executeStatement('DELETE FROM equipment_loan');
        $em->getConnection()->executeStatement('DELETE FROM review');
        $em->getConnection()->executeStatement('DELETE FROM reset_password_request');

        foreach ($userRepository->findAll() as $user) {
            $em->remove($user);
        }

        $em->flush();

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $container->get('security.user_password_hasher');

        $user = (new User())
            ->setEmail('email@example.com')
            ->setFirstName('Test')
            ->setLastName('User')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setActive(true)
            ->setRoles(['ROLE_USER']);
        $this->validPassword = self::generateTestPassword();
        $user->setPassword($passwordHasher->hashPassword($user, $this->validPassword));

        $em->persist($user);
        $em->flush();
    }

    public function testLogin(): void
    {
        $invalidPassword = self::generateTestPassword();
        while ($invalidPassword === $this->validPassword) {
            $invalidPassword = self::generateTestPassword();
        }

        $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Se connecter', [
            '_username' => 'doesNotExist@example.com',
            '_password' => $this->validPassword,
        ]);

        self::assertResponseRedirects('/login');
        $this->client->followRedirect();

        self::assertSelectorTextContains('.alert-danger', 'Identifiants invalides.');

        $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Se connecter', [
            '_username' => 'email@example.com',
            '_password' => $invalidPassword,
        ]);

        self::assertResponseRedirects('/login');
        $this->client->followRedirect();

        self::assertSelectorTextContains('.alert-danger', 'Identifiants invalides.');

        $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Se connecter', [
            '_username' => 'email@example.com',
            '_password' => $this->validPassword,
        ]);

        self::assertResponseRedirects('/');
        $this->client->followRedirect();

        self::assertSelectorNotExists('.alert-danger');
    }

    public function testInactiveUserCannotLogin(): void
    {
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);
        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = static::getContainer()->get('security.user_password_hasher');

        $inactivePassword = self::generateTestPassword();
        $inactiveUser = (new User())
            ->setEmail('inactive@example.com')
            ->setFirstName('Inactive')
            ->setLastName('User')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setActive(false)
            ->setRoles(['ROLE_USER']);
        $inactiveUser->setPassword($passwordHasher->hashPassword($inactiveUser, $inactivePassword));

        $em->persist($inactiveUser);
        $em->flush();

        $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Se connecter', [
            '_username' => 'inactive@example.com',
            '_password' => $inactivePassword,
        ]);

        self::assertResponseRedirects('/login');

        $this->client->followRedirect();

        self::assertSelectorTextContains('.alert-danger', 'Votre compte est desactive.');
    }
}

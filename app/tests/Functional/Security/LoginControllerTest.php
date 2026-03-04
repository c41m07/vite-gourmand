<?php
namespace App\Tests\Functional\Security;

use App\Entity\User;
use App\Tests\Support\GeneratesTestPasswords;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginControllerTest extends WebTestCase
{
    use GeneratesTestPasswords;

    private KernelBrowser $client;
    private string $validPassword;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        $userRepository = $em->getRepository(User::class);

        $em->getConnection()->executeStatement('DELETE FROM reset_password_request');

        // Remove any existing users from the test database
        foreach ($userRepository->findAll() as $user) {
            $em->remove($user);
        }

        $em->flush();

        // Create a User fixture
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

        // Denied - Can't login with invalid email address.
        $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Se connecter', [
            '_username' => 'doesNotExist@example.com',
            '_password' => $this->validPassword,
        ]);

        self::assertResponseRedirects('/login');
        $this->client->followRedirect();

        // Ensure we do not reveal if the user exists or not.
        self::assertSelectorTextContains('.alert-danger', 'Identifiants invalides.');

        // Denied - Can't login with invalid password.
        $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Se connecter', [
            '_username' => 'email@example.com',
            '_password' => $invalidPassword,
        ]);

        self::assertResponseRedirects('/login');
        $this->client->followRedirect();

        // Ensure we do not reveal the user exists but the password is wrong.
        self::assertSelectorTextContains('.alert-danger', 'Identifiants invalides.');

        // Success - Login with valid credentials is allowed.
        $this->client->submitForm('Se connecter', [
            '_username' => 'email@example.com',
            '_password' => $this->validPassword,
        ]);

        self::assertResponseRedirects('/');
        $this->client->followRedirect();

        self::assertSelectorNotExists('.alert-danger');
    }
}


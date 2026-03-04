<?php

namespace App\Tests\Functional\Security;

use App\Entity\User;
use App\Tests\Support\GeneratesTestPasswords;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class BackOfficeAccessControlTest extends WebTestCase
{
    use GeneratesTestPasswords;

    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->passwordHasher = static::getContainer()->get('security.user_password_hasher');

        $this->entityManager->getConnection()->executeStatement('DELETE FROM reset_password_request');

        foreach ($this->entityManager->getRepository(User::class)->findAll() as $user) {
            $this->entityManager->remove($user);
        }
        $this->entityManager->flush();
    }

    public function testAnonymousCannotAccessBackOfficeRoutes(): void
    {
        $this->client->request('GET', '/admin');
        self::assertResponseRedirects('/login');

        $this->client->request('GET', '/worker');
        self::assertResponseRedirects('/login');
    }

    #[DataProvider('provideAuthenticatedAccessCases')]
    public function testAuthenticatedAccessByRole(string $role, string $path, int $expectedStatus): void
    {
        $user = $this->createUser([$role], sprintf('%s.%s@example.com', strtolower($role), md5($path)));
        $this->client->loginUser($user);

        $this->client->request('GET', $path);

        if (200 === $expectedStatus) {
            self::assertResponseIsSuccessful();

            return;
        }

        self::assertResponseStatusCodeSame($expectedStatus);
    }

    public static function provideAuthenticatedAccessCases(): iterable
    {
        yield 'user denied admin' => ['ROLE_USER', '/admin', 403];
        yield 'user denied worker' => ['ROLE_USER', '/worker', 403];
        yield 'worker denied admin' => ['ROLE_WORKER', '/admin', 403];
        yield 'worker allowed worker' => ['ROLE_WORKER', '/worker', 200];
        yield 'admin allowed admin' => ['ROLE_ADMIN', '/admin', 200];
        yield 'admin allowed worker via hierarchy' => ['ROLE_ADMIN', '/worker', 200];
    }

    private function createUser(array $roles, string $email): User
    {
        $user = (new User())
            ->setEmail($email)
            ->setFirstName('Test')
            ->setLastName('User')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setActive(true)
            ->setRoles($roles);

        $user->setPassword($this->passwordHasher->hashPassword($user, self::generateTestPassword()));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}


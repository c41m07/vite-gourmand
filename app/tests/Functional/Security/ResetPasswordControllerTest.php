<?php

namespace App\Tests\Functional\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Support\GeneratesTestPasswordsTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordControllerTest extends WebTestCase
{
    use GeneratesTestPasswordsTrait;

    private KernelBrowser $client;
    private EntityManagerInterface $em;
    private UserRepository $userRepository;

    public function testResetPasswordController(): void
    {
        $initialPassword = self::generateTestPassword();
        $newPassword = self::generateTestPassword();
        while ($newPassword === $initialPassword) {
            $newPassword = self::generateTestPassword();
        }

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $now = new \DateTime();
        $user = new User()
            ->setEmail('me@exemple.fr')
            ->setFirstName('Albert')
            ->setLastName('Einstein')
            ->setRoles(['ROLE_USER'])
            ->setActive(true)
            ->setCreatedAt($now)
            ->setUpdatedAt($now);

        $user->setPassword($passwordHasher->hashPassword($user, $initialPassword));

        $this->em->persist($user);
        $this->em->flush();

        $this->client->request('GET', '/reset-password');

        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('Réinitialiser votre mot de passe');

        $this->client->submitForm('Envoyer le lien de réinitialisation', [
            'reset_password_request_form[email]' => 'me@exemple.fr',
        ]);

        self::assertResponseRedirects('/reset-password/check-email');

        $emailMessage = self::getMailerMessage();
        self::assertEmailAddressContains($emailMessage, 'from', 'no-reply@vite-gourmand.test');
        self::assertEmailAddressContains($emailMessage, 'to', 'me@exemple.fr');
        self::assertEmailHtmlBodyContains($emailMessage, 'Ce lien expirera dans');

        self::assertResponseRedirects('/reset-password/check-email');

        $crawler = $this->client->followRedirect();

        self::assertPageTitleContains('Email de réinitialisation envoyé');
        self::assertStringContainsString('Ce lien expirera dans 1 heure', $crawler->html());

        $emailHtml = (string) $emailMessage->getHtmlBody();
        preg_match('#href="([^"]*/reset-password/reset/[^"]+)"#', $emailHtml, $resetLink);
        self::assertArrayHasKey(1, $resetLink);

        $this->client->request('GET', $resetLink[1]);

        self::assertResponseRedirects('/reset-password/reset');

        $this->client->followRedirect();

        $this->client->submitForm('Confirmer', [
            'change_password_form[plainPassword][first]' => $newPassword,
            'change_password_form[plainPassword][second]' => $newPassword,
        ]);

        self::assertResponseRedirects('/login');

        $user = $this->userRepository->findOneBy(['email' => 'me@exemple.fr']);

        self::assertInstanceOf(User::class, $user);

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        self::assertTrue($passwordHasher->isPasswordValid($user, $newPassword));
    }

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $container = static::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $this->em = $em;

        $em->getConnection()->executeStatement('DELETE FROM reset_password_request');
        $this->userRepository = $container->get(UserRepository::class);

        foreach ($this->userRepository->findAll() as $user) {
            $this->em->remove($user);
        }

        $this->em->flush();
    }
}

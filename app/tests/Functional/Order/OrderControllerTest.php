<?php

namespace App\Tests\Functional\Order;

use App\Entity\Media;
use App\Entity\Menu;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class OrderControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private int $menuId;
    private int $outOfStockMenuId;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        foreach ($this->entityManager->getRepository(Menu::class)->findAll() as $menu) {
            $this->entityManager->remove($menu);
        }
        foreach ($this->entityManager->getRepository(Media::class)->findAll() as $media) {
            $this->entityManager->remove($media);
        }
        foreach ($this->entityManager->getRepository(User::class)->findAll() as $user) {
            $this->entityManager->remove($user);
        }
        $this->entityManager->flush();

        $now = new \DateTime();

        $media = (new Media())
            ->setImgUrl('/build/images/test-order-menu.jpg')
            ->setAltText('Menu commande test')
            ->setHash('test-order-menu')
            ->setCreatedAt($now);

        $menu = (new Menu())
            ->setTitle('Menu Commande Test')
            ->setDescription('Description commande test')
            ->setMinPeople(4)
            ->setBasePrice(5200)
            ->setConditionInfo('Commande 48h a l avance.')
            ->setStock(5)
            ->setActive(true)
            ->setCreatedAt($now)
            ->setUpdatedAt($now)
            ->setMedia($media);

        $outOfStockMedia = (new Media())
            ->setImgUrl('/build/images/test-order-menu-out.jpg')
            ->setAltText('Menu commande sans stock')
            ->setHash('test-order-menu-out')
            ->setCreatedAt($now);

        $outOfStockMenu = (new Menu())
            ->setTitle('Menu Sans Stock')
            ->setDescription('Description menu sans stock')
            ->setMinPeople(2)
            ->setBasePrice(4100)
            ->setConditionInfo('Menu actuellement indisponible.')
            ->setStock(0)
            ->setActive(true)
            ->setCreatedAt($now)
            ->setUpdatedAt($now)
            ->setMedia($outOfStockMedia);

        $this->entityManager->persist($media);
        $this->entityManager->persist($outOfStockMedia);
        $this->entityManager->persist($menu);
        $this->entityManager->persist($outOfStockMenu);
        $this->entityManager->flush();

        $this->menuId = (int) $menu->getId();
        $this->outOfStockMenuId = (int) $outOfStockMenu->getId();
    }

    public function testOrderPageRedirectsAnonymousUserToLogin(): void
    {
        $this->client->request('GET', '/order/new/' . $this->menuId);

        self::assertResponseRedirects('/login');
    }

    public function testOrderPageDisplaysSelectedMenuForAuthenticatedUser(): void
    {
        $user = (new User())
            ->setEmail('order.user@example.com')
            ->setFirstName('Order')
            ->setLastName('User')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setActive(true)
            ->setRoles(['ROLE_USER']);

        $user->setPassword($this->passwordHasher->hashPassword($user, 'Order1234*/'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->request('GET', '/order/new/' . $this->menuId);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Commander Menu Commande Test');
        self::assertSelectorTextContains('.card-body', 'Menu selectionne');
        self::assertStringContainsString('Commande 48h a l avance.', (string) $this->client->getResponse()->getContent());
    }

    public function testOrderPageRedirectsToMenuIndexWhenStockIsEmpty(): void
    {
        $user = (new User())
            ->setEmail('order.stock@example.com')
            ->setFirstName('Order')
            ->setLastName('Stock')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setActive(true)
            ->setRoles(['ROLE_USER']);

        $user->setPassword($this->passwordHasher->hashPassword($user, 'Order1234*/'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->request('GET', '/order/new/' . $this->outOfStockMenuId);

        self::assertResponseRedirects('/menu/');

        $crawler = $this->client->followRedirect();

        self::assertPageTitleContains('Menus');
        self::assertStringContainsString('Ce menu n est plus disponible a la commande.', $crawler->html());
    }
}

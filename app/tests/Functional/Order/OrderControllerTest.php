<?php

namespace App\Tests\Functional\Order;

use App\Entity\CustomerOrder;
use App\Entity\CustomerOrderMenu;
use App\Entity\CustomerOrderStatusHistory;
use App\Entity\EquipmentLoan;
use App\Entity\EquipmentLoanStatus;
use App\Entity\Media;
use App\Entity\Menu;
use App\Entity\OrderStatus;
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

        $this->cleanupDatabase();

        if (null === $this->entityManager->getRepository(OrderStatus::class)->findOneBy(['code' => 'pending'])) {
            $pendingStatus = (new OrderStatus())
                ->setCode('pending')
                ->setLabel('En attente');

            $this->entityManager->persist($pendingStatus);
        }

        if (null === $this->entityManager->getRepository(EquipmentLoanStatus::class)->findOneBy(['status' => 'Emprunte'])) {
            $equipmentLoanStatus = (new EquipmentLoanStatus())
                ->setStatus('Emprunte');

            $this->entityManager->persist($equipmentLoanStatus);
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
            ->setConditionInfo('Commande 48 h à l\'avance.')
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

    protected function tearDown(): void
    {
        if (isset($this->entityManager) && $this->entityManager->isOpen()) {
            $this->cleanupDatabase();
        }

        parent::tearDown();
    }

    private function cleanupDatabase(): void
    {
        foreach ($this->entityManager->getRepository(CustomerOrderStatusHistory::class)->findAll() as $statusHistory) {
            $this->entityManager->remove($statusHistory);
        }
        foreach ($this->entityManager->getRepository(CustomerOrderMenu::class)->findAll() as $orderMenu) {
            $this->entityManager->remove($orderMenu);
        }
        foreach ($this->entityManager->getRepository(CustomerOrder::class)->findAll() as $order) {
            $this->entityManager->remove($order);
        }
        foreach ($this->entityManager->getRepository(EquipmentLoan::class)->findAll() as $equipmentLoan) {
            $this->entityManager->remove($equipmentLoan);
        }
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
        self::assertSelectorTextContains('.card-body', 'Menu sélectionné');
        self::assertSelectorTextContains('.order-entry__conditions-list', 'Commande 48 h à l\'avance.');
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
        self::assertStringContainsString('Ce menu n\'est plus disponible à la commande.', $crawler->html());
    }

    public function testAuthenticatedUserCanCreateOrderWithEquipmentLoan(): void
    {
        $user = (new User())
            ->setEmail('order.loan@example.com')
            ->setFirstName('Order')
            ->setLastName('Loan')
            ->setPhone('0601020304')
            ->setPostalAddress('10 rue des Tests')
            ->setCity('Bordeaux')
            ->setPostalCode('33000')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setActive(true)
            ->setRoles(['ROLE_USER']);

        $user->setPassword($this->passwordHasher->hashPassword($user, 'Order1234*/'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/order/new/' . $this->menuId);
        $form = $crawler->filter('form#order-entry-form')->form([
            'order_form[phone]' => '0611223344',
            'order_form[deliveryAddress]' => '15 quai des Tests',
            'order_form[deliveryCity]' => 'Merignac',
            'order_form[deliveryPostalCode]' => '33700',
            'order_form[serviceDate]' => '2026-03-20',
            'order_form[serviceTime]' => '12:30',
            'order_form[peopleCount]' => '4',
            'order_form[distancekm]' => '10',
            'order_form[needEquipmentLoan]' => '1',
            'order_form[equipmentLoanStartAt]' => '2026-03-20T10:00',
            'order_form[equipmentLoanEndAt]' => '2026-03-21T18:00',
            'order_form[equipmentLoanNote]' => 'Prevoir vaisselle et nappes.',
            'order_form[note]' => 'Acces par le portail arriere.',
        ]);

        $this->client->submit($form);

        self::assertResponseRedirects('/user/profile?tab=orders');

        $order = $this->entityManager->getRepository(CustomerOrder::class)->findOneBy(
            ['user' => $user],
            ['id' => 'DESC']
        );

        self::assertNotNull($order);
        self::assertSame('0611223344', $order->getPhone());
        self::assertSame('Merignac', $order->getDeliveryCity());
        self::assertSame('Acces par le portail arriere.', $order->getNote());
        self::assertNotNull($order->getEquipmentLoan());
        self::assertSame('Prevoir vaisselle et nappes.', $order->getEquipmentLoan()?->getNote());
        self::assertSame('Emprunte', $order->getEquipmentLoan()?->getStatus()?->getStatus());
    }
}

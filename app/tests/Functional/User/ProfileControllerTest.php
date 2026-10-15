<?php

namespace App\Tests\Functional\User;

use App\Entity\CustomerOrder;
use App\Entity\CustomerOrderMenu;
use App\Entity\CustomerOrderStatusHistory;
use App\Entity\Media;
use App\Entity\Menu;
use App\Entity\OrderStatus;
use App\Entity\User;
use App\Tests\Support\GeneratesTestPasswordsTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileControllerTest extends WebTestCase
{
    use GeneratesTestPasswordsTrait;

    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $this->entityManager->getConnection()->executeStatement('DELETE FROM reset_password_request');

        $this->cleanupDatabase();
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
        foreach ($this->entityManager->getRepository(Menu::class)->findAll() as $menu) {
            $this->entityManager->remove($menu);
        }
        foreach ($this->entityManager->getRepository(Media::class)->findAll() as $media) {
            $this->entityManager->remove($media);
        }
        foreach ($this->entityManager->getRepository(OrderStatus::class)->findAll() as $orderStatus) {
            $this->entityManager->remove($orderStatus);
        }
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
        $userPassword = self::generateTestPassword();

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = static::getContainer()->get('security.user_password_hasher');

        $user = $this->createUser(
            'profile.user@example.com',
            'Profile',
            'User',
            $userPassword,
            $passwordHasher
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->request('GET', '/user/profile?tab=profile');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Mon compte');
        self::assertSelectorTextContains('.account-section-title', 'Informations personnelles');
        self::assertStringContainsString('profile.user@example.com', (string) $this->client->getResponse()->getContent());
    }

    public function testEditProfileAccessIsDeniedForAnonymous(): void
    {
        $this->client->request('GET', '/user/profile/edit');

        self::assertResponseRedirects('/login');
    }

    public function testEditProfileAccessIsAllowedForRoleUser(): void
    {
        $userPassword = self::generateTestPassword();

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = static::getContainer()->get('security.user_password_hasher');

        $user = $this->createUser(
            'profile.user@example.com',
            'Profile',
            'User',
            $userPassword,
            $passwordHasher
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->request('GET', '/user/profile/edit');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Modifier mon profil');
        self::assertSelectorExists('form[name="profile_edit_form"]');
        self::assertSelectorExists('#profile_edit_form_currentPassword');
    }

    public function testCancelOrderWithInvalidCsrfDoesNotCancelOrder(): void
    {
        $userPassword = self::generateTestPassword();

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = static::getContainer()->get('security.user_password_hasher');

        $user = $this->createUser(
            'profile.order@example.com',
            'Profile',
            'Order',
            $userPassword,
            $passwordHasher
        );
        $this->entityManager->persist($user);

        $order = $this->createPendingOrderForUser($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->request('POST', '/user/order/' . $order->getId() . '/cancel', [
            '_token' => 'invalid-token',
        ]);

        self::assertResponseRedirects('/user/order/' . $order->getId());

        $this->client->followRedirect();
        self::assertSelectorTextContains('.alert-danger', 'Une erreur est survenue lors de la tentative d\'annulation de la commande.');

        $this->entityManager->clear();

        $persistedOrder = $this->entityManager->getRepository(CustomerOrder::class)->find($order->getId());
        self::assertInstanceOf(CustomerOrder::class, $persistedOrder);
        self::assertCount(1, $persistedOrder->getCustomerOrderStatusHistories());
        self::assertSame(
            'pending',
            $persistedOrder->getCustomerOrderStatusHistories()->last()?->getOrderStatus()?->getCode()
        );
    }

    public function testCancelOrderWithValidCsrfCancelsPendingOrder(): void
    {
        $userPassword = self::generateTestPassword();

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = static::getContainer()->get('security.user_password_hasher');

        $user = $this->createUser(
            'profile.cancel@example.com',
            'Profile',
            'Cancel',
            $userPassword,
            $passwordHasher
        );
        $this->entityManager->persist($user);

        $order = $this->createPendingOrderForUser($user);
        $this->entityManager->flush();

        $userId = $user->getId();
        $orderId = $order->getId();
        $this->entityManager->clear();

        $user = $this->entityManager->getRepository(User::class)->find($userId);
        $order = $this->entityManager->getRepository(CustomerOrder::class)->find($orderId);
        self::assertInstanceOf(User::class, $user);
        self::assertInstanceOf(CustomerOrder::class, $order);

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', '/user/order/' . $order->getId());

        self::assertResponseIsSuccessful();
        self::assertSelectorExists(sprintf('form[action="/user/order/%d/cancel"]', $order->getId()));

        $form = $crawler->filter(sprintf('form[action="/user/order/%d/cancel"]', $order->getId()))->form();
        $this->client->submit($form);

        self::assertResponseRedirects('/user/profile?tab=orders');

        $this->entityManager->clear();

        $persistedOrder = $this->entityManager->getRepository(CustomerOrder::class)->find($order->getId());
        self::assertInstanceOf(CustomerOrder::class, $persistedOrder);
        self::assertCount(2, $persistedOrder->getCustomerOrderStatusHistories());
        self::assertSame(
            'cancelled',
            $persistedOrder->getCustomerOrderStatusHistories()->last()?->getOrderStatus()?->getCode()
        );
    }

    private function createUser(
        string $email,
        string $firstName,
        string $lastName,
        string $plainPassword,
        UserPasswordHasherInterface $passwordHasher,
    ): User {
        $user = (new User())
            ->setEmail($email)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setActive(true)
            ->setRoles(['ROLE_USER']);

        $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

        return $user;
    }

    private function createPendingOrderForUser(User $user): CustomerOrder
    {
        $now = new \DateTime();

        $pendingStatus = (new OrderStatus())
            ->setCode('pending')
            ->setLabel('En attente');
        $cancelledStatus = (new OrderStatus())
            ->setCode('cancelled')
            ->setLabel('Annulée');

        $media = (new Media())
            ->setImgUrl('/build/images/test-profile-order.jpg')
            ->setAltText('Menu profil test')
            ->setHash('profile-order-test')
            ->setCreatedAt($now);

        $menu = (new Menu())
            ->setTitle('Menu Profil Test')
            ->setDescription('Description commande profil')
            ->setMinPeople(4)
            ->setBasePrice(5200)
            ->setConditionInfo('Commande 48 h à l\'avance.')
            ->setStock(5)
            ->setActive(true)
            ->setCreatedAt($now)
            ->setUpdatedAt($now)
            ->setMedia($media);

        $order = (new CustomerOrder())
            ->setUser($user)
            ->setOrderedAt(clone $now)
            ->setServiceDate(clone $now)
            ->setserviceTime('12:30')
            ->setPeopleCount(4)
            ->setPhone('0611223344')
            ->setDeliveryAddress('15 quai des Tests')
            ->setDeliveryCity('Bordeaux')
            ->setDeliveryPostalCode('33000')
            ->setDeliveryPrice(0)
            ->setDiscountAmount(0)
            ->setTotalPrice(20800)
            ->setCreatedAt(clone $now)
            ->setUpdatedAt(clone $now);

        $orderMenu = (new CustomerOrderMenu())
            ->setCustomerOrder($order)
            ->setMenu($menu)
            ->setQuantity(1)
            ->setUnitPrice(5200)
            ->setLineTotal(20800);

        $history = (new CustomerOrderStatusHistory())
            ->setCustomerOrder($order)
            ->setOrderStatus($pendingStatus)
            ->setChangedByUser($user)
            ->setChangedAt(clone $now)
            ->setComment('Commande créée pour le test.');

        $this->entityManager->persist($pendingStatus);
        $this->entityManager->persist($cancelledStatus);
        $this->entityManager->persist($media);
        $this->entityManager->persist($menu);
        $this->entityManager->persist($order);
        $this->entityManager->persist($orderMenu);
        $this->entityManager->persist($history);

        return $order;
    }
}

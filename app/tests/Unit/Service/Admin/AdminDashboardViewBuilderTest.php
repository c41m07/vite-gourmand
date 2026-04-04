<?php

namespace App\Tests\Unit\Service\Admin;

use App\Service\Admin\AdminDashboardViewBuilder;
use App\Entity\CustomerOrder;
use App\Entity\CustomerOrderStatusHistory;
use App\Entity\Menu;
use App\Entity\OrderStatus;
use App\Entity\Review;
use App\Entity\User;
use App\Repository\CustomerOrderRepository;
use App\Repository\MenuRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use DateTime;
use PHPUnit\Framework\TestCase;

final class AdminDashboardViewBuilderTest extends TestCase
{
    public function testBuildAggregatesDashboardMetricsAndNormalizesTab(): void
    {
        $admin = $this->createUser('admin@example.com', ['ROLE_ADMIN']);
        $worker = $this->createUser('worker@example.com', ['ROLE_WORKER']);
        $client = $this->createUser('client@example.com', ['ROLE_USER']);

        $pendingStatus = (new OrderStatus())
            ->setCode('pending')
            ->setLabel('En attente');

        $completedStatus = (new OrderStatus())
            ->setCode('completed')
            ->setLabel('Terminee');

        $orderInProgress = $this->createOrder(12500, $client, $pendingStatus);
        $completedOrder = $this->createOrder(8300, $client, $completedStatus);
        $orderWithoutStatus = (new CustomerOrder())
            ->setUser($client)
            ->setTotalPrice(5400);

        $activeMenu = (new Menu())->setActive(true);
        $inactiveMenu = (new Menu())->setActive(false);

        $firstReview = (new Review())
            ->setRating(5)
            ->setTitle('Excellent')
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime())
            ->setValidated(true)
            ->setUser($client);

        $secondReview = (new Review())
            ->setRating(3)
            ->setTitle('Bien')
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime())
            ->setValidated(true)
            ->setUser($client);

        $customerOrderRepository = $this->createStub(CustomerOrderRepository::class);
        $customerOrderRepository
            ->method('findBy')
            ->with([], ['serviceDate' => 'DESC'])
            ->willReturn([$orderInProgress, $completedOrder, $orderWithoutStatus]);

        $menuRepository = $this->createStub(MenuRepository::class);
        $menuRepository
            ->method('findBy')
            ->with([], ['id' => 'DESC'])
            ->willReturn([$activeMenu, $inactiveMenu]);

        $reviewRepository = $this->createStub(ReviewRepository::class);
        $reviewRepository
            ->method('findAll')
            ->willReturn([$firstReview, $secondReview]);

        $userRepository = $this->createStub(UserRepository::class);
        $userRepository
            ->method('findAll')
            ->willReturn([$admin, $worker, $client]);

        $builder = new AdminDashboardViewBuilder(
            $customerOrderRepository,
            $menuRepository,
            $reviewRepository,
            $userRepository,
        );

        $viewData = $builder->build('unknown-tab');

        self::assertSame('dashboard', $viewData->activeTab);
        self::assertCount(2, $viewData->employees);
        self::assertCount(3, $viewData->recentOrders);
        self::assertSame(3, $viewData->totalOrders);
        self::assertSame(1, $viewData->clientsCount);
        self::assertSame(1, $viewData->ordersInProgress);
        self::assertSame(26200, $viewData->revenueTotal);
        self::assertSame(4.0, $viewData->avgRating);
        self::assertSame(1, $viewData->activeMenus);
        self::assertSame($orderInProgress, $viewData->recentOrders[0]);
    }

    public function testBuildKeepsEmployeesTabWhenRequested(): void
    {
        $customerOrderRepository = $this->createStub(CustomerOrderRepository::class);
        $customerOrderRepository
            ->method('findBy')
            ->willReturn([]);

        $menuRepository = $this->createStub(MenuRepository::class);
        $menuRepository
            ->method('findBy')
            ->willReturn([]);

        $reviewRepository = $this->createStub(ReviewRepository::class);
        $reviewRepository
            ->method('findAll')
            ->willReturn([]);

        $userRepository = $this->createStub(UserRepository::class);
        $userRepository
            ->method('findAll')
            ->willReturn([]);

        $builder = new AdminDashboardViewBuilder(
            $customerOrderRepository,
            $menuRepository,
            $reviewRepository,
            $userRepository,
        );

        $viewData = $builder->build('employees');

        self::assertSame('employees', $viewData->activeTab);
    }

    private function createUser(string $email, array $roles): User
    {
        return (new User())
            ->setEmail($email)
            ->setPassword('password')
            ->setFirstName('Test')
            ->setLastName('User')
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime())
            ->setActive(true)
            ->setRoles($roles);
    }

    private function createOrder(int $totalPrice, User $user, OrderStatus $status): CustomerOrder
    {
        $order = (new CustomerOrder())
            ->setUser($user)
            ->setTotalPrice($totalPrice)
            ->setServiceDate(new DateTime())
            ->setOrderedAt(new DateTime())
            ->setPeopleCount(4)
            ->setDeliveryAddress('10 rue des tests')
            ->setDeliveryCity('Bordeaux')
            ->setDeliveryPostalCode('33000')
            ->setDeliveryPrice(0)
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime())
            ->setserviceTime('12:30');

        $history = (new CustomerOrderStatusHistory())
            ->setCustomerOrder($order)
            ->setOrderStatus($status)
            ->setChangedAt(new DateTime())
            ->setComment('Statut de test');

        $order->addCustomerOrderStatusHistory($history);

        return $order;
    }
}

<?php

namespace App\Service\User;

use App\Entity\CustomerOrder;
use App\Entity\CustomerOrderStatusHistory;
use App\Entity\User;
use App\Repository\OrderStatusRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class UserOrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrderStatusRepository $orderStatusRepository,
    ) {
    }

    public function isOwnedBy(CustomerOrder $order, User $user): bool
    {
        return $order->getUser()?->getId() === $user->getId();
    }

    public function canBeModified(CustomerOrder $order): bool
    {
        return $order->isPending();
    }

    public function cancel(CustomerOrder $order, User $user): void
    {
        $cancelledStatus = $this->orderStatusRepository->findOneBy(['code' => 'cancelled']);
        if (null === $cancelledStatus) {
            throw new \RuntimeException('Status non trouvé');
        }

        $history = (new CustomerOrderStatusHistory())
            ->setCustomerOrder($order)
            ->setOrderStatus($cancelledStatus)
            ->setChangedByUser($user)
            ->setChangedAt(new \DateTime())
            ->setComment('Commande annulée par le client.');

        $this->entityManager->persist($history);
        $this->entityManager->flush();
    }
}

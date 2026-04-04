<?php

namespace App\DataFixtures;

use App\Entity\CustomerOrder;
use App\Entity\CustomerOrderMenu;
use App\Entity\CustomerOrderStatusHistory;
use App\Entity\Menu;
use App\Entity\OrderStatus;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

final class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->seed(20260307);

        foreach ($this->getOrderDefinitions() as $definition) {
            $user = $this->getReference($definition['user'], User::class);
            $orderedAt = $definition['orderedAt'];

            $order = $this->createOrder($faker, $user, $definition, $orderedAt);
            $manager->persist($order);

            $lineTotal = 0;
            foreach ($definition['menus'] as $menuDefinition) {
                $menu = $this->getReference($menuDefinition['menu'], Menu::class);
                $unitPrice = (int) ($menu->getBasePrice() ?? 0);
                $quantity = (int) $menuDefinition['quantity'];
                $lineTotal += $unitPrice * $quantity;

                $orderMenu = (new CustomerOrderMenu())
                    ->setCustomerOrder($order)
                    ->setMenu($menu)
                    ->setQuantity($quantity)
                    ->setUnitPrice($unitPrice)
                    ->setLineTotal($unitPrice * $quantity);
                $manager->persist($orderMenu);
            }

            $discountAmount = (int) ($definition['discountAmount'] ?? 0);
            $deliveryPrice = (int) $definition['deliveryPrice'];
            $order->setDiscountAmount($discountAmount);
            $order->setTotalPrice(max(0, $lineTotal + $deliveryPrice - $discountAmount));

            foreach ($definition['statusHistory'] as $historyDefinition) {
                $changedBy = null;
                if (isset($historyDefinition['changedBy'])) {
                    $changedBy = $this->getReference($historyDefinition['changedBy'], User::class);
                }

                $history = (new CustomerOrderStatusHistory())
                    ->setCustomerOrder($order)
                    ->setOrderStatus($this->getReference($historyDefinition['status'], OrderStatus::class))
                    ->setChangedByUser($changedBy)
                    ->setChangedAt($historyDefinition['changedAt'])
                    ->setComment($historyDefinition['comment']);
                $manager->persist($history);
            }
        }

        $manager->flush();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getOrderDefinitions(): array
    {
        return [
            [
                'user' => DemoUserFixtures::USER_REFERENCE,
                'orderedAt' => new \DateTime('2026-02-28 10:15:00'),
                'serviceDate' => new \DateTime('2026-03-12'),
                'serviceTime' => '12:30',
                'peopleCount' => 12,
                'deliveryAddress' => '18 quai des Chartrons',
                'deliveryCity' => 'Bordeaux',
                'deliveryPostalCode' => '33000',
                'deliveryPrice' => 1500,
                'discountAmount' => 0,
                'note' => 'Merci de prévoir une installation à l\'intérieur.',
                'menus' => [
                    ['menu' => MenuFixtures::MENU_RECEPTION_BORDELAISE, 'quantity' => 1],
                ],
                'statusHistory' => [
                    [
                        'status' => ReferenceFixtures::ORDER_STATUS_PENDING,
                        'changedAt' => new \DateTime('2026-02-28 10:15:00'),
                        'changedBy' => DemoUserFixtures::USER_REFERENCE,
                        'comment' => 'Commande enregistrée depuis le compte client.',
                    ],
                    [
                        'status' => ReferenceFixtures::ORDER_STATUS_ACCEPTED,
                        'changedAt' => new \DateTime('2026-02-28 14:00:00'),
                        'changedBy' => DemoUserFixtures::ADMIN_REFERENCE,
                        'comment' => 'Validation des disponibilités et du nombre de convives.',
                    ],
                ],
            ],
            [
                'user' => DemoUserFixtures::USER_REFERENCE,
                'orderedAt' => new \DateTime('2026-02-24 09:20:00'),
                'serviceDate' => new \DateTime('2026-03-09'),
                'serviceTime' => '19:00',
                'peopleCount' => 18,
                'deliveryAddress' => '42 rue Notre-Dame',
                'deliveryCity' => 'Bordeaux',
                'deliveryPostalCode' => '33000',
                'deliveryPrice' => 2000,
                'discountAmount' => 500,
                'note' => 'Accès camion possible par la cour.',
                'menus' => [
                    ['menu' => MenuFixtures::MENU_STREET_FOOD, 'quantity' => 1],
                    ['menu' => MenuFixtures::MENU_BRUNCH, 'quantity' => 1],
                ],
                'statusHistory' => [
                    [
                        'status' => ReferenceFixtures::ORDER_STATUS_PENDING,
                        'changedAt' => new \DateTime('2026-02-24 09:20:00'),
                        'changedBy' => DemoUserFixtures::USER_REFERENCE,
                        'comment' => 'Demande initiale reçue.',
                    ],
                    [
                        'status' => ReferenceFixtures::ORDER_STATUS_ACCEPTED,
                        'changedAt' => new \DateTime('2026-02-24 12:10:00'),
                        'changedBy' => DemoUserFixtures::ADMIN_REFERENCE,
                        'comment' => 'Commande confirmée après appel client.',
                    ],
                    [
                        'status' => ReferenceFixtures::ORDER_STATUS_PREPARING,
                        'changedAt' => new \DateTime('2026-03-07 08:30:00'),
                        'changedBy' => DemoUserFixtures::WORKER_REFERENCE,
                        'comment' => 'Production lancée en cuisine.',
                    ],
                ],
            ],
            [
                'user' => DemoUserFixtures::WORKER_REFERENCE,
                'orderedAt' => new \DateTime('2026-02-18 11:45:00'),
                'serviceDate' => new \DateTime('2026-03-08'),
                'serviceTime' => '11:30',
                'peopleCount' => 10,
                'deliveryAddress' => '5 cours Georges-Clemenceau',
                'deliveryCity' => 'Bordeaux',
                'deliveryPostalCode' => '33000',
                'deliveryPrice' => 1200,
                'discountAmount' => 0,
                'note' => 'Installation rapide souhaitée avant midi.',
                'menus' => [
                    ['menu' => MenuFixtures::MENU_PROTEIN, 'quantity' => 1],
                ],
                'statusHistory' => [
                    [
                        'status' => ReferenceFixtures::ORDER_STATUS_PENDING,
                        'changedAt' => new \DateTime('2026-02-18 11:45:00'),
                        'changedBy' => DemoUserFixtures::WORKER_REFERENCE,
                        'comment' => 'Commande créée depuis un compte employé.',
                    ],
                    [
                        'status' => ReferenceFixtures::ORDER_STATUS_ACCEPTED,
                        'changedAt' => new \DateTime('2026-02-18 15:15:00'),
                        'changedBy' => DemoUserFixtures::ADMIN_REFERENCE,
                        'comment' => 'Validation budget et production.',
                    ],
                    [
                        'status' => ReferenceFixtures::ORDER_STATUS_DELIVERING,
                        'changedAt' => new \DateTime('2026-03-07 17:45:00'),
                        'changedBy' => DemoUserFixtures::WORKER_REFERENCE,
                        'comment' => 'Commande partie en livraison.',
                    ],
                ],
            ],
            [
                'user' => DemoUserFixtures::ADMIN_REFERENCE,
                'orderedAt' => new \DateTime('2026-01-30 16:00:00'),
                'serviceDate' => new \DateTime('2026-02-15'),
                'serviceTime' => '20:00',
                'peopleCount' => 30,
                'deliveryAddress' => '10 allée de Tourny',
                'deliveryCity' => 'Bordeaux',
                'deliveryPostalCode' => '33000',
                'deliveryPrice' => 2500,
                'discountAmount' => 1000,
                'note' => 'Prestation pour réception privée.',
                'menus' => [
                    ['menu' => MenuFixtures::MENU_FESTIVE, 'quantity' => 2],
                ],
                'statusHistory' => [
                    [
                        'status' => ReferenceFixtures::ORDER_STATUS_PENDING,
                        'changedAt' => new \DateTime('2026-01-30 16:00:00'),
                        'changedBy' => DemoUserFixtures::ADMIN_REFERENCE,
                        'comment' => 'Saisie initiale.',
                    ],
                    [
                        'status' => ReferenceFixtures::ORDER_STATUS_ACCEPTED,
                        'changedAt' => new \DateTime('2026-01-31 09:00:00'),
                        'changedBy' => DemoUserFixtures::ADMIN_REFERENCE,
                        'comment' => 'Acompte reçu.',
                    ],
                    [
                        'status' => ReferenceFixtures::ORDER_STATUS_PREPARING,
                        'changedAt' => new \DateTime('2026-02-14 10:00:00'),
                        'changedBy' => DemoUserFixtures::WORKER_REFERENCE,
                        'comment' => 'Derniers préparatifs effectués.',
                    ],
                    [
                        'status' => ReferenceFixtures::ORDER_STATUS_DELIVERED,
                        'changedAt' => new \DateTime('2026-02-15 22:30:00'),
                        'changedBy' => DemoUserFixtures::WORKER_REFERENCE,
                        'comment' => 'Livraison terminée et confirmée.',
                    ],
                    [
                        'status' => ReferenceFixtures::ORDER_STATUS_COMPLETED,
                        'changedAt' => new \DateTime('2026-02-16 09:00:00'),
                        'changedBy' => DemoUserFixtures::ADMIN_REFERENCE,
                        'comment' => 'Commande terminée.',
                    ],
                ],
            ],
            [
                'user' => DemoUserFixtures::USER_REFERENCE,
                'orderedAt' => new \DateTime('2026-01-18 08:40:00'),
                'serviceDate' => new \DateTime('2026-01-27'),
                'serviceTime' => '13:00',
                'peopleCount' => 8,
                'deliveryAddress' => '27 rue Sainte-Catherine',
                'deliveryCity' => 'Bordeaux',
                'deliveryPostalCode' => '33000',
                'deliveryPrice' => 1500,
                'discountAmount' => 0,
                'note' => 'Anniversaire en petit comité.',
                'menus' => [
                    ['menu' => MenuFixtures::MENU_VEGETARIAN_GARDEN, 'quantity' => 1],
                ],
                'statusHistory' => [
                    [
                        'status' => ReferenceFixtures::ORDER_STATUS_PENDING,
                        'changedAt' => new \DateTime('2026-01-18 08:40:00'),
                        'changedBy' => DemoUserFixtures::USER_REFERENCE,
                        'comment' => 'Demande client en attente de validation.',
                    ],
                    [
                        'status' => ReferenceFixtures::ORDER_STATUS_CANCELLED,
                        'changedAt' => new \DateTime('2026-01-19 10:10:00'),
                        'changedBy' => DemoUserFixtures::ADMIN_REFERENCE,
                        'comment' => 'Annulation à la demande du client.',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $definition
     */
    private function createOrder(
        Generator $faker,
        User $user,
        array $definition,
        \DateTime $orderedAt,
    ): CustomerOrder {
        $serviceDate = clone $definition['serviceDate'];

        return (new CustomerOrder())
            ->setUser($user)
            ->setOrderedAt(clone $orderedAt)
            ->setServiceDate($serviceDate)
            ->setserviceTime($definition['serviceTime'])
            ->setPeopleCount($definition['peopleCount'])
            ->setDeliveryAddress($definition['deliveryAddress'])
            ->setDeliveryCity($definition['deliveryCity'])
            ->setDeliveryPostalCode($definition['deliveryPostalCode'])
            ->setDeliveryPrice($definition['deliveryPrice'])
            ->setNote($definition['note'] ?? $faker->optional(0.4)->sentence())
            ->setCreatedAt(clone $orderedAt)
            ->setUpdatedAt(clone $orderedAt);
    }

    public function getDependencies(): array
    {
        return [
            DemoUserFixtures::class,
            ReferenceFixtures::class,
            MenuFixtures::class,
        ];
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\CustomerOrder;
use App\Entity\CustomerOrderMenu;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

final class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->seed(20260307);

        foreach ($this->getReviewDefinitions() as $definition) {
            $user = $this->getReference($definition['user'], \App\Entity\User::class);
            $menu = $this->getReference($definition['menu'], \App\Entity\Menu::class);
            $createdAt = $faker->dateTimeBetween('-3 months', '-5 days');

            $review = (new Review())
                ->setUser($user)
                ->setRating($definition['rating'])
                ->setTitle($definition['title'])
                ->setDescription($definition['description'])
                ->setValidated($definition['validated'])
                ->setCreatedAt(clone $createdAt)
                ->setUpdatedAt(clone $createdAt);
            $manager->persist($review);

            $order = $this->createCustomerOrder($faker, $user, $menu->getBasePrice() ?? 0, $createdAt);
            $manager->persist($order);

            $orderMenu = (new CustomerOrderMenu())
                ->setCustomerOrder($order)
                ->setMenu($menu)
                ->setReview($review)
                ->setQuantity(1)
                ->setUnitPrice($menu->getBasePrice() ?? 0)
                ->setLineTotal($menu->getBasePrice() ?? 0);
            $manager->persist($orderMenu);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DemoUserFixtures::class,
            ReferenceFixtures::class,
            MenuFixtures::class,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getReviewDefinitions(): array
    {
        return [
            [
                'user' => DemoUserFixtures::USER_REFERENCE,
                'menu' => MenuFixtures::MENU_RECEPTION_BORDELAISE,
                'rating' => 5,
                'title' => 'Une prestation irreprochable',
                'description' => 'Buffet genereux, equipe ponctuelle et saveurs tres justes. Tous les invites ont adore.',
                'validated' => true,
            ],
            [
                'user' => DemoUserFixtures::USER_REFERENCE,
                'menu' => MenuFixtures::MENU_ITALIAN_DOLCE_VITA,
                'rating' => 4,
                'title' => 'Tres bon buffet italien',
                'description' => 'Des plats gourmands et bien presentes. Mention speciale pour la panna cotta.',
                'validated' => true,
            ],
            [
                'user' => DemoUserFixtures::USER_REFERENCE,
                'menu' => MenuFixtures::MENU_VEGETARIAN_GARDEN,
                'rating' => 5,
                'title' => 'Option vegetarienne convaincante',
                'description' => 'Un menu equilibre, colore et vraiment savoureux. Aucun convive ne sest senti prive.',
                'validated' => false,
            ],
            [
                'user' => DemoUserFixtures::WORKER_REFERENCE,
                'menu' => MenuFixtures::MENU_ASIAN_SHARING,
                'rating' => 4,
                'title' => 'Parfait pour un dejeuner d equipe',
                'description' => 'Format pratique a partager, portions genereuses et tres bon rapport qualite prix.',
                'validated' => true,
            ],
            [
                'user' => DemoUserFixtures::ADMIN_REFERENCE,
                'menu' => MenuFixtures::MENU_FESTIVE,
                'rating' => 5,
                'title' => 'Ideal pour un evenement important',
                'description' => 'Presentation soignee, produits de qualite et execution sans faute du debut a la fin.',
                'validated' => true,
            ],
            [
                'user' => DemoUserFixtures::ADMIN_REFERENCE,
                'menu' => MenuFixtures::MENU_BRUNCH,
                'rating' => 3,
                'title' => 'Belle base, quelques ajustements a faire',
                'description' => 'Le concept plait beaucoup mais nous attendons encore des retours avant publication.',
                'validated' => false,
            ],
        ];
    }

    private function createCustomerOrder(
        Generator $faker,
        \App\Entity\User $user,
        int $basePrice,
        \DateTime $createdAt
    ): CustomerOrder {
        $serviceDate = (clone $createdAt);
        $serviceDate->modify('+7 days');

        return (new CustomerOrder())
            ->setUser($user)
            ->setOrderedAt(clone $createdAt)
            ->setServiceDate($serviceDate)
            ->setserviceTime($faker->randomElement(['12:00', '12:30', '19:00', '19:30']))
            ->setPeopleCount($faker->numberBetween(6, 24))
            ->setDeliveryAddress($faker->streetAddress())
            ->setDeliveryCity('Bordeaux')
            ->setDeliveryPostalCode('33000')
            ->setDeliveryPrice(1500)
            ->setDiscountAmount(0)
            ->setTotalPrice($basePrice + 1500)
            ->setNote($faker->optional()->sentence())
            ->setCreatedAt(clone $createdAt)
            ->setUpdatedAt(clone $createdAt);
    }
}

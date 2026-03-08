<?php

namespace App\DataFixtures;

use App\Entity\Allergen;
use App\Entity\Diet;
use App\Entity\DishType;
use App\Entity\EquipmentLoanStatus;
use App\Entity\OrderStatus;
use App\Entity\Theme;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class ReferenceFixtures extends Fixture
{
    public const ORDER_STATUS_PENDING = 'order-status-pending';
    public const ORDER_STATUS_ACCEPTED = 'order-status-accepted';
    public const ORDER_STATUS_PREPARING = 'order-status-preparing';
    public const ORDER_STATUS_DELIVERING = 'order-status-delivering';
    public const ORDER_STATUS_DELIVERED = 'order-status-delivered';
    public const ORDER_STATUS_AWAITING_EQUIPMENT_RETURN = 'order-status-awaiting-equipment-return';
    public const ORDER_STATUS_COMPLETED = 'order-status-completed';
    public const ORDER_STATUS_CANCELLED = 'order-status-cancelled';

    public const DISH_TYPE_STARTER = 'dish-type-starter';
    public const DISH_TYPE_MAIN = 'dish-type-main';
    public const DISH_TYPE_SIDE = 'dish-type-side';
    public const DISH_TYPE_DESSERT = 'dish-type-dessert';
    public const DISH_TYPE_DRINK = 'dish-type-drink';

    public const THEME_FRENCH = 'theme-french';
    public const THEME_ITALIAN = 'theme-italian';
    public const THEME_ASIAN = 'theme-asian';
    public const THEME_STREET_FOOD = 'theme-street-food';
    public const THEME_SEASONAL = 'theme-seasonal';
    public const THEME_FESTIVE = 'theme-festive';

    public const DIET_CLASSIC = 'diet-classic';
    public const DIET_VEGETARIAN = 'diet-vegetarian';
    public const DIET_VEGAN = 'diet-vegan';
    public const DIET_GLUTEN_FREE = 'diet-gluten-free';
    public const DIET_HIGH_PROTEIN = 'diet-high-protein';

    public const ALLERGEN_GLUTEN = 'allergen-gluten';
    public const ALLERGEN_MILK = 'allergen-milk';
    public const ALLERGEN_EGGS = 'allergen-eggs';
    public const ALLERGEN_PEANUTS = 'allergen-peanuts';
    public const ALLERGEN_NUTS = 'allergen-nuts';
    public const ALLERGEN_SOY = 'allergen-soy';
    public const ALLERGEN_FISH = 'allergen-fish';
    public const ALLERGEN_SHELLFISH = 'allergen-shellfish';
    public const ALLERGEN_MUSTARD = 'allergen-mustard';
    public const ALLERGEN_SESAME = 'allergen-sesame';

    public const EQUIPMENT_LOAN_AVAILABLE = 'equipment-loan-available';
    public const EQUIPMENT_LOAN_BORROWED = 'equipment-loan-borrowed';
    public const EQUIPMENT_LOAN_RETURNED = 'equipment-loan-returned';
    public const EQUIPMENT_LOAN_LOST = 'equipment-loan-lost';

    public function load(ObjectManager $manager): void
    {
        $this->loadOrderStatuses($manager);
        $this->loadDishTypes($manager);
        $this->loadThemes($manager);
        $this->loadDiets($manager);
        $this->loadAllergens($manager);
        $this->loadEquipmentLoanStatuses($manager);

        $manager->flush();
    }

    private function loadOrderStatuses(ObjectManager $manager): void
    {
        $statuses = [
            self::ORDER_STATUS_PENDING => ['code' => 'pending', 'label' => 'En attente'],
            self::ORDER_STATUS_ACCEPTED => ['code' => 'accepted', 'label' => 'Acceptée'],
            self::ORDER_STATUS_PREPARING => ['code' => 'preparing', 'label' => 'En préparation'],
            self::ORDER_STATUS_DELIVERING => ['code' => 'delivering', 'label' => 'En cours de livraison'],
            self::ORDER_STATUS_DELIVERED => ['code' => 'delivered', 'label' => 'Livrée'],
            self::ORDER_STATUS_AWAITING_EQUIPMENT_RETURN => ['code' => 'awaiting_equipment_return', 'label' => 'En attente du retour de matériel'],
            self::ORDER_STATUS_COMPLETED => ['code' => 'completed', 'label' => 'Terminée'],
            self::ORDER_STATUS_CANCELLED => ['code' => 'cancelled', 'label' => 'Annulée'],
        ];

        foreach ($statuses as $reference => $data) {
            $status = (new OrderStatus())
                ->setCode($data['code'])
                ->setLabel($data['label']);

            $manager->persist($status);
            $this->addReference($reference, $status);
        }
    }

    private function loadDishTypes(ObjectManager $manager): void
    {
        $dishTypes = [
            self::DISH_TYPE_STARTER => 'Entree',
            self::DISH_TYPE_MAIN => 'Plat',
            self::DISH_TYPE_SIDE => 'Accompagnement',
            self::DISH_TYPE_DESSERT => 'Dessert',
            self::DISH_TYPE_DRINK => 'Boisson',
        ];

        foreach ($dishTypes as $reference => $name) {
            $dishType = (new DishType())->setName($name);

            $manager->persist($dishType);
            $this->addReference($reference, $dishType);
        }
    }

    private function loadThemes(ObjectManager $manager): void
    {
        $themes = [
            self::THEME_FRENCH => 'Cuisine francaise',
            self::THEME_ITALIAN => 'Cuisine italienne',
            self::THEME_ASIAN => 'Cuisine asiatique',
            self::THEME_STREET_FOOD => 'Street food',
            self::THEME_SEASONAL => 'Cuisine de saison',
            self::THEME_FESTIVE => 'Buffet festif',
        ];

        foreach ($themes as $reference => $name) {
            $theme = (new Theme())->setName($name);

            $manager->persist($theme);
            $this->addReference($reference, $theme);
        }
    }

    private function loadDiets(ObjectManager $manager): void
    {
        $diets = [
            self::DIET_CLASSIC => 'Classique',
            self::DIET_VEGETARIAN => 'Vegetarien',
            self::DIET_VEGAN => 'Vegan',
            self::DIET_GLUTEN_FREE => 'Sans gluten',
            self::DIET_HIGH_PROTEIN => 'Riche en proteines',
        ];

        foreach ($diets as $reference => $name) {
            $diet = (new Diet())->setName($name);

            $manager->persist($diet);
            $this->addReference($reference, $diet);
        }
    }

    private function loadAllergens(ObjectManager $manager): void
    {
        $allergens = [
            self::ALLERGEN_GLUTEN => 'Gluten',
            self::ALLERGEN_MILK => 'Lait',
            self::ALLERGEN_EGGS => 'Oeufs',
            self::ALLERGEN_PEANUTS => 'Arachides',
            self::ALLERGEN_NUTS => 'Fruits a coque',
            self::ALLERGEN_SOY => 'Soja',
            self::ALLERGEN_FISH => 'Poisson',
            self::ALLERGEN_SHELLFISH => 'Crustaces',
            self::ALLERGEN_MUSTARD => 'Moutarde',
            self::ALLERGEN_SESAME => 'Sesame',
        ];

        foreach ($allergens as $reference => $name) {
            $allergen = (new Allergen())->setName($name);

            $manager->persist($allergen);
            $this->addReference($reference, $allergen);
        }
    }

    private function loadEquipmentLoanStatuses(ObjectManager $manager): void
    {
        $statuses = [
            self::EQUIPMENT_LOAN_AVAILABLE => 'Disponible',
            self::EQUIPMENT_LOAN_BORROWED => 'Emprunte',
            self::EQUIPMENT_LOAN_RETURNED => 'Retourne',
            self::EQUIPMENT_LOAN_LOST => 'Perdu',
        ];

        foreach ($statuses as $reference => $statusLabel) {
            $status = (new EquipmentLoanStatus())->setStatus($statusLabel);

            $manager->persist($status);
            $this->addReference($reference, $status);
        }
    }
}

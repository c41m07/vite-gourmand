<?php

namespace App\DataFixtures;

use App\Entity\Dish;
use App\Entity\DishAllergen;
use App\Entity\Media;
use App\Entity\Menu;
use App\Entity\MenuDish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

final class MenuFixtures extends Fixture implements DependentFixtureInterface
{
    public const MENU_RECEPTION_BORDELAISE = 'menu-reception-bordelaise';
    public const MENU_ITALIAN_DOLCE_VITA = 'menu-italian-dolce-vita';
    public const MENU_ASIAN_SHARING = 'menu-asian-sharing';
    public const MENU_VEGETARIAN_GARDEN = 'menu-vegetarian-garden';
    public const MENU_STREET_FOOD = 'menu-street-food';
    public const MENU_BRUNCH = 'menu-brunch';
    public const MENU_FESTIVE = 'menu-festive';
    public const MENU_PROTEIN = 'menu-protein';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->seed(20260307);

        foreach ($this->getMenuDefinitions() as $reference => $definition) {
            $menu = $this->createMenu($manager, $faker, $definition);
            $manager->persist($menu);
            $this->addReference($reference, $menu);

            foreach ($definition['dishes'] as $dishDefinition) {
                $dish = $this->createDish($faker, $dishDefinition);
                $manager->persist($dish);

                $menuDish = (new MenuDish())
                    ->setMenu($menu)
                    ->setDish($dish);
                $manager->persist($menuDish);

                foreach ($dishDefinition['allergens'] as $allergenReference) {
                    $dishAllergen = (new DishAllergen())
                        ->setDish($dish)
                        ->setAllergen($this->getReference($allergenReference, \App\Entity\Allergen::class));
                    $manager->persist($dishAllergen);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ReferenceFixtures::class,
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getMenuDefinitions(): array
    {
        return [
            self::MENU_RECEPTION_BORDELAISE => [
                'title' => 'Reception bordelaise',
                'theme' => ReferenceFixtures::THEME_FRENCH,
                'diet' => ReferenceFixtures::DIET_CLASSIC,
                'minPeople' => 10,
                'basePrice' => 2400,
                'stock' => 18,
                'active' => true,
                'mediaAlt' => 'Reception bordelaise',
                'dishes' => [
                    [
                        'name' => 'Veloute de saison',
                        'type' => ReferenceFixtures::DISH_TYPE_STARTER,
                        'allergens' => [ReferenceFixtures::ALLERGEN_MILK],
                    ],
                    [
                        'name' => 'Poulet roti fermier',
                        'type' => ReferenceFixtures::DISH_TYPE_MAIN,
                        'allergens' => [ReferenceFixtures::ALLERGEN_MUSTARD],
                    ],
                    [
                        'name' => 'Gratin dauphinois',
                        'type' => ReferenceFixtures::DISH_TYPE_SIDE,
                        'allergens' => [ReferenceFixtures::ALLERGEN_MILK],
                    ],
                    [
                        'name' => 'Tarte fine aux pommes',
                        'type' => ReferenceFixtures::DISH_TYPE_DESSERT,
                        'allergens' => [ReferenceFixtures::ALLERGEN_GLUTEN, ReferenceFixtures::ALLERGEN_EGGS],
                    ],
                ],
            ],
            self::MENU_ITALIAN_DOLCE_VITA => [
                'title' => 'Dolce vita italienne',
                'theme' => ReferenceFixtures::THEME_ITALIAN,
                'diet' => ReferenceFixtures::DIET_CLASSIC,
                'minPeople' => 8,
                'basePrice' => 2700,
                'stock' => 14,
                'active' => true,
                'mediaAlt' => 'Buffet italien',
                'dishes' => [
                    [
                        'name' => 'Bruschetta tomate basilic',
                        'type' => ReferenceFixtures::DISH_TYPE_STARTER,
                        'allergens' => [ReferenceFixtures::ALLERGEN_GLUTEN],
                    ],
                    [
                        'name' => 'Lasagnes maison',
                        'type' => ReferenceFixtures::DISH_TYPE_MAIN,
                        'allergens' => [ReferenceFixtures::ALLERGEN_GLUTEN, ReferenceFixtures::ALLERGEN_MILK, ReferenceFixtures::ALLERGEN_EGGS],
                    ],
                    [
                        'name' => 'Legumes grilles',
                        'type' => ReferenceFixtures::DISH_TYPE_SIDE,
                        'allergens' => [],
                    ],
                    [
                        'name' => 'Panna cotta vanille',
                        'type' => ReferenceFixtures::DISH_TYPE_DESSERT,
                        'allergens' => [ReferenceFixtures::ALLERGEN_MILK],
                    ],
                ],
            ],
            self::MENU_ASIAN_SHARING => [
                'title' => 'Table asiatique a partager',
                'theme' => ReferenceFixtures::THEME_ASIAN,
                'diet' => ReferenceFixtures::DIET_CLASSIC,
                'minPeople' => 12,
                'basePrice' => 2900,
                'stock' => 20,
                'active' => true,
                'mediaAlt' => 'Selection asiatique',
                'dishes' => [
                    [
                        'name' => 'Gyozas legumes',
                        'type' => ReferenceFixtures::DISH_TYPE_STARTER,
                        'allergens' => [ReferenceFixtures::ALLERGEN_GLUTEN, ReferenceFixtures::ALLERGEN_SOY],
                    ],
                    [
                        'name' => 'Poulet teriyaki',
                        'type' => ReferenceFixtures::DISH_TYPE_MAIN,
                        'allergens' => [ReferenceFixtures::ALLERGEN_SOY, ReferenceFixtures::ALLERGEN_SESAME],
                    ],
                    [
                        'name' => 'Riz sesame',
                        'type' => ReferenceFixtures::DISH_TYPE_SIDE,
                        'allergens' => [ReferenceFixtures::ALLERGEN_SESAME],
                    ],
                    [
                        'name' => 'Perles coco mangue',
                        'type' => ReferenceFixtures::DISH_TYPE_DESSERT,
                        'allergens' => [],
                    ],
                ],
            ],
            self::MENU_VEGETARIAN_GARDEN => [
                'title' => 'Jardin vegetarien',
                'theme' => ReferenceFixtures::THEME_SEASONAL,
                'diet' => ReferenceFixtures::DIET_VEGETARIAN,
                'minPeople' => 6,
                'basePrice' => 2300,
                'stock' => 16,
                'active' => true,
                'mediaAlt' => 'Menu vegetarien',
                'dishes' => [
                    [
                        'name' => 'Houmous de betterave',
                        'type' => ReferenceFixtures::DISH_TYPE_STARTER,
                        'allergens' => [ReferenceFixtures::ALLERGEN_SESAME],
                    ],
                    [
                        'name' => 'Parmentier de lentilles',
                        'type' => ReferenceFixtures::DISH_TYPE_MAIN,
                        'allergens' => [ReferenceFixtures::ALLERGEN_MILK],
                    ],
                    [
                        'name' => 'Poelee de legumes verts',
                        'type' => ReferenceFixtures::DISH_TYPE_SIDE,
                        'allergens' => [],
                    ],
                    [
                        'name' => 'Moelleux chocolat noisette',
                        'type' => ReferenceFixtures::DISH_TYPE_DESSERT,
                        'allergens' => [ReferenceFixtures::ALLERGEN_EGGS, ReferenceFixtures::ALLERGEN_NUTS],
                    ],
                ],
            ],
            self::MENU_STREET_FOOD => [
                'title' => 'Street food party',
                'theme' => ReferenceFixtures::THEME_STREET_FOOD,
                'diet' => ReferenceFixtures::DIET_CLASSIC,
                'minPeople' => 15,
                'basePrice' => 2100,
                'stock' => 25,
                'active' => true,
                'mediaAlt' => 'Street food',
                'dishes' => [
                    [
                        'name' => 'Mini tacos croustillants',
                        'type' => ReferenceFixtures::DISH_TYPE_STARTER,
                        'allergens' => [ReferenceFixtures::ALLERGEN_GLUTEN],
                    ],
                    [
                        'name' => 'Burger effiloche',
                        'type' => ReferenceFixtures::DISH_TYPE_MAIN,
                        'allergens' => [ReferenceFixtures::ALLERGEN_GLUTEN, ReferenceFixtures::ALLERGEN_MUSTARD],
                    ],
                    [
                        'name' => 'Potatoes paprika',
                        'type' => ReferenceFixtures::DISH_TYPE_SIDE,
                        'allergens' => [],
                    ],
                    [
                        'name' => 'Brookie caramel',
                        'type' => ReferenceFixtures::DISH_TYPE_DESSERT,
                        'allergens' => [ReferenceFixtures::ALLERGEN_GLUTEN, ReferenceFixtures::ALLERGEN_EGGS, ReferenceFixtures::ALLERGEN_MILK],
                    ],
                ],
            ],
            self::MENU_BRUNCH => [
                'title' => 'Brunch des copains',
                'theme' => ReferenceFixtures::THEME_FESTIVE,
                'diet' => ReferenceFixtures::DIET_CLASSIC,
                'minPeople' => 8,
                'basePrice' => 2600,
                'stock' => 12,
                'active' => false,
                'mediaAlt' => 'Brunch genereux',
                'dishes' => [
                    [
                        'name' => 'Granola maison',
                        'type' => ReferenceFixtures::DISH_TYPE_STARTER,
                        'allergens' => [ReferenceFixtures::ALLERGEN_NUTS],
                    ],
                    [
                        'name' => 'Brioche perdue salee',
                        'type' => ReferenceFixtures::DISH_TYPE_MAIN,
                        'allergens' => [ReferenceFixtures::ALLERGEN_GLUTEN, ReferenceFixtures::ALLERGEN_EGGS, ReferenceFixtures::ALLERGEN_MILK],
                    ],
                    [
                        'name' => 'Salade croquante',
                        'type' => ReferenceFixtures::DISH_TYPE_SIDE,
                        'allergens' => [ReferenceFixtures::ALLERGEN_MUSTARD],
                    ],
                    [
                        'name' => 'Cookie geant a partager',
                        'type' => ReferenceFixtures::DISH_TYPE_DESSERT,
                        'allergens' => [ReferenceFixtures::ALLERGEN_GLUTEN, ReferenceFixtures::ALLERGEN_EGGS, ReferenceFixtures::ALLERGEN_MILK],
                    ],
                ],
            ],
            self::MENU_FESTIVE => [
                'title' => 'Buffet festif signature',
                'theme' => ReferenceFixtures::THEME_FESTIVE,
                'diet' => ReferenceFixtures::DIET_CLASSIC,
                'minPeople' => 20,
                'basePrice' => 3500,
                'stock' => 10,
                'active' => true,
                'mediaAlt' => 'Buffet festif premium',
                'dishes' => [
                    [
                        'name' => 'Saumon gravelax',
                        'type' => ReferenceFixtures::DISH_TYPE_STARTER,
                        'allergens' => [ReferenceFixtures::ALLERGEN_FISH],
                    ],
                    [
                        'name' => 'Filet de boeuf sauce poivre',
                        'type' => ReferenceFixtures::DISH_TYPE_MAIN,
                        'allergens' => [ReferenceFixtures::ALLERGEN_MILK],
                    ],
                    [
                        'name' => 'Millefeuille de pommes de terre',
                        'type' => ReferenceFixtures::DISH_TYPE_SIDE,
                        'allergens' => [ReferenceFixtures::ALLERGEN_MILK],
                    ],
                    [
                        'name' => 'Entremets chocolat praliné',
                        'type' => ReferenceFixtures::DISH_TYPE_DESSERT,
                        'allergens' => [ReferenceFixtures::ALLERGEN_GLUTEN, ReferenceFixtures::ALLERGEN_EGGS, ReferenceFixtures::ALLERGEN_NUTS],
                    ],
                ],
            ],
            self::MENU_PROTEIN => [
                'title' => 'Forme et proteines',
                'theme' => ReferenceFixtures::THEME_SEASONAL,
                'diet' => ReferenceFixtures::DIET_HIGH_PROTEIN,
                'minPeople' => 6,
                'basePrice' => 2800,
                'stock' => 9,
                'active' => true,
                'mediaAlt' => 'Menu riche en proteines',
                'dishes' => [
                    [
                        'name' => 'Oeufs cocotte epinards',
                        'type' => ReferenceFixtures::DISH_TYPE_STARTER,
                        'allergens' => [ReferenceFixtures::ALLERGEN_EGGS, ReferenceFixtures::ALLERGEN_MILK],
                    ],
                    [
                        'name' => 'Poulet citron herbes',
                        'type' => ReferenceFixtures::DISH_TYPE_MAIN,
                        'allergens' => [],
                    ],
                    [
                        'name' => 'Quinoa croquant',
                        'type' => ReferenceFixtures::DISH_TYPE_SIDE,
                        'allergens' => [],
                    ],
                    [
                        'name' => 'Skyr fruits rouges',
                        'type' => ReferenceFixtures::DISH_TYPE_DESSERT,
                        'allergens' => [ReferenceFixtures::ALLERGEN_MILK],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $definition
     */
    private function createMenu(ObjectManager $manager, Generator $faker, array $definition): Menu
    {
        $createdAt = $faker->dateTimeBetween('-4 months', '-2 weeks');
        $media = (new Media())
            ->setImgUrl($this->buildMenuImagePath($definition['title']))
            ->setAltText($definition['mediaAlt'])
            ->setHash(md5($definition['title']))
            ->setCreatedAt(clone $createdAt);
        $manager->persist($media);

        $menu = (new Menu())
            ->setTitle($definition['title'])
            ->setDescription($faker->paragraphs(2, true))
            ->setMinPeople($definition['minPeople'])
            ->setBasePrice($definition['basePrice'])
            ->setConditionInfo($faker->sentence())
            ->setStock($definition['stock'])
            ->setActive($definition['active'])
            ->setCreatedAt(clone $createdAt)
            ->setUpdatedAt(clone $createdAt)
            ->setTheme($this->getReference($definition['theme'], \App\Entity\Theme::class))
            ->setDiet($this->getReference($definition['diet'], \App\Entity\Diet::class))
            ->setMedia($media);

        return $menu;
    }

    /**
     * @param array<string, mixed> $definition
     */
    private function createDish(Generator $faker, array $definition): Dish
    {
        return (new Dish())
            ->setName($definition['name'])
            ->setDescription($faker->sentence(12))
            ->setActive(true)
            ->setDishType($this->getReference($definition['type'], \App\Entity\DishType::class));
    }

    private function buildMenuImagePath(string $title): string
    {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim((string) $slug, '-');

        return sprintf('/build/images/menus/%s.png', $slug);
    }
}

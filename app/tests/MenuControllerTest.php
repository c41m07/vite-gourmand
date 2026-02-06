<?php

namespace App\Tests;

use App\Entity\Media;
use App\Entity\Menu;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MenuControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private int $menuId;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');

        foreach ($this->entityManager->getRepository(Menu::class)->findAll() as $menu) {
            $this->entityManager->remove($menu);
        }
        foreach ($this->entityManager->getRepository(Media::class)->findAll() as $media) {
            $this->entityManager->remove($media);
        }
        $this->entityManager->flush();

        $now = new \DateTime();
        $media = (new Media())
            ->setImgUrl('/build/images/test-menu.jpg')
            ->setAltText('Menu test')
            ->setHash('test-hash')
            ->setCreatedAt($now);

        $menu = (new Menu())
            ->setTitle('Menu Test')
            ->setDescription('Test menu description')
            ->setMinPeople(2)
            ->setBasePrice(4500)
            ->setConditionInfo('Test conditions')
            ->setStock(10)
            ->setActive(true)
            ->setCreatedAt($now)
            ->setUpdatedAt($now)
            ->setMedia($media);

        $this->entityManager->persist($media);
        $this->entityManager->persist($menu);
        $this->entityManager->flush();

        $this->menuId = (int) $menu->getId();
    }

    public function testMenuIndexLoads(): void
    {
        $this->client->request('GET', '/menu/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Tous nos menus');
        self::assertSelectorExists('.menu-card');
        self::assertSelectorTextContains('.menu-card .card-title', 'Menu Test');
    }

    public function testMenuShowLoads(): void
    {
        $this->client->request('GET', '/menu/' . $this->menuId);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Menu Test');
        self::assertSelectorTextContains('.menu-show__intro h2', 'Description');
    }
}

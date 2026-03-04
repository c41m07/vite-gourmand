<?php

namespace App\Tests\Functional\Menu;

use App\Entity\Diet;
use App\Entity\Media;
use App\Entity\Menu;
use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MenuApiControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private int $themeId1;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        foreach ($this->entityManager->getRepository(Menu::class)->findAll() as $menu) {
            $this->entityManager->remove($menu);
        }
        foreach ($this->entityManager->getRepository(Media::class)->findAll() as $media) {
            $this->entityManager->remove($media);
        }
        foreach ($this->entityManager->getRepository(Theme::class)->findAll() as $theme) {
            $this->entityManager->remove($theme);
        }
        foreach ($this->entityManager->getRepository(Diet::class)->findAll() as $diet) {
            $this->entityManager->remove($diet);
        }
        $this->entityManager->flush();

        $now = new \DateTime();

        $theme1 = (new Theme())->setName('Theme A');
        $theme2 = (new Theme())->setName('Theme B');
        $diet1 = (new Diet())->setName('Diet A');
        $diet2 = (new Diet())->setName('Diet B');

        $media1 = (new Media())
            ->setImgUrl('/build/images/test-menu-1.jpg')
            ->setAltText('Menu 1')
            ->setHash('hash-1')
            ->setCreatedAt($now);

        $media2 = (new Media())
            ->setImgUrl('/build/images/test-menu-2.jpg')
            ->setAltText('Menu 2')
            ->setHash('hash-2')
            ->setCreatedAt($now);

        $media3 = (new Media())
            ->setImgUrl('/build/images/test-menu-3.jpg')
            ->setAltText('Menu 3')
            ->setHash('hash-3')
            ->setCreatedAt($now);

        $menu1 = (new Menu())
            ->setTitle('Menu One')
            ->setDescription('Menu one description')
            ->setMinPeople(2)
            ->setBasePrice(2500)
            ->setConditionInfo('Menu one conditions')
            ->setStock(10)
            ->setActive(true)
            ->setCreatedAt($now)
            ->setUpdatedAt($now)
            ->setTheme($theme1)
            ->setDiet($diet1)
            ->setMedia($media1);

        $menu2 = (new Menu())
            ->setTitle('Menu Two')
            ->setDescription('Menu two description')
            ->setMinPeople(6)
            ->setBasePrice(3500)
            ->setConditionInfo('Menu two conditions')
            ->setStock(5)
            ->setActive(true)
            ->setCreatedAt($now)
            ->setUpdatedAt($now)
            ->setTheme($theme2)
            ->setDiet($diet2)
            ->setMedia($media2);

        $menu3 = (new Menu())
            ->setTitle('Menu Inactive')
            ->setDescription('Menu inactive description')
            ->setMinPeople(4)
            ->setBasePrice(4500)
            ->setConditionInfo('Menu inactive conditions')
            ->setStock(20)
            ->setActive(false)
            ->setCreatedAt($now)
            ->setUpdatedAt($now)
            ->setTheme($theme1)
            ->setDiet($diet1)
            ->setMedia($media3);

        $this->entityManager->persist($theme1);
        $this->entityManager->persist($theme2);
        $this->entityManager->persist($diet1);
        $this->entityManager->persist($diet2);
        $this->entityManager->persist($media1);
        $this->entityManager->persist($media2);
        $this->entityManager->persist($media3);
        $this->entityManager->persist($menu1);
        $this->entityManager->persist($menu2);
        $this->entityManager->persist($menu3);
        $this->entityManager->flush();

        $this->themeId1 = (int) $theme1->getId();
    }

    public function testApiMenusList(): void
    {
        $this->client->request('GET', '/api/menus');

        self::assertResponseIsSuccessful();

        $data = json_decode($this->client->getResponse()->getContent(), true);
        self::assertIsArray($data);
        self::assertArrayHasKey('body', $data);
        self::assertArrayHasKey('count', $data);
        self::assertSame(2, $data['count']);

        self::assertStringContainsString('Menu One', $data['body']);
        self::assertStringContainsString('Menu Two', $data['body']);
        self::assertStringNotContainsString('Menu Inactive', $data['body']);
    }

    public function testApiMenusFilterByMinPrice(): void
    {
        $this->client->request('GET', '/api/menus?minPrice=3000');

        self::assertResponseIsSuccessful();

        $data = json_decode($this->client->getResponse()->getContent(), true);
        self::assertIsArray($data);
        self::assertArrayHasKey('body', $data);
        self::assertArrayHasKey('count', $data);
        self::assertSame(1, $data['count']);
        self::assertStringContainsString('Menu Two', $data['body']);
        self::assertStringNotContainsString('Menu One', $data['body']);
        self::assertStringNotContainsString('Menu Inactive', $data['body']);
    }

    public function testApiMenusFilterByTheme(): void
    {
        $this->client->request('GET', '/api/menus?theme=' . $this->themeId1);

        self::assertResponseIsSuccessful();

        $data = json_decode($this->client->getResponse()->getContent(), true);
        self::assertIsArray($data);
        self::assertArrayHasKey('body', $data);
        self::assertArrayHasKey('count', $data);
        self::assertSame(1, $data['count']);
        self::assertStringContainsString('Menu One', $data['body']);
        self::assertStringNotContainsString('Menu Two', $data['body']);
        self::assertStringNotContainsString('Menu Inactive', $data['body']);
    }

    public function testApiMenusIgnoresIsActiveQueryParam(): void
    {
        $this->client->request('GET', '/api/menus?isActive=false');

        self::assertResponseIsSuccessful();

        $data = json_decode($this->client->getResponse()->getContent(), true);
        self::assertIsArray($data);
        self::assertArrayHasKey('body', $data);
        self::assertArrayHasKey('count', $data);
        self::assertSame(2, $data['count']);

        self::assertStringContainsString('Menu One', $data['body']);
        self::assertStringContainsString('Menu Two', $data['body']);
        self::assertStringNotContainsString('Menu Inactive', $data['body']);
    }
}


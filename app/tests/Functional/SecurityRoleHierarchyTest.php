<?php

namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\InMemoryUser;

final class SecurityRoleHierarchyTest extends KernelTestCase
{
    public function testAdminInheritsWorkerRole(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $tokenStorage = $container->get(TokenStorageInterface::class);
        $authorizationChecker = $container->get(AuthorizationCheckerInterface::class);
        $admin = new InMemoryUser('admin@exemple.test', null, ['ROLE_ADMIN']);
        $tokenStorage->setToken(new UsernamePasswordToken($admin, 'main', ['ROLE_ADMIN']));

        self::assertTrue($authorizationChecker->isGranted('ROLE_USER'));
        self::assertTrue($authorizationChecker->isGranted('ROLE_WORKER'));
        self::assertTrue($authorizationChecker->isGranted('ROLE_ADMIN'));
    }

}

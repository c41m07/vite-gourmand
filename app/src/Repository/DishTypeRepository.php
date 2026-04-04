<?php

namespace App\Repository;

use App\Entity\DishType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DishType>
 */
class DishTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DishType::class);
    }

    public function findDishesByMenu(\App\Entity\Menu $menu)
    {
        return $this->createQueryBuilder('dt')->addSelect('dt')
            ->leftJoin('dt.dishes', 'd')->addSelect('d')
            ->leftJoin('d.menuDishes', 'md')->addSelect('md')
            ->where('md.menu = :menu')
            ->setParameter('menu', $menu)
            ->getQuery()
            ->getResult();
    }
}

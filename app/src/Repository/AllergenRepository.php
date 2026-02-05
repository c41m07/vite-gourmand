<?php

namespace App\Repository;

use App\Entity\Allergen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Allergen>
 */
class AllergenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Allergen::class);
    }

    public function findByMenu(\App\Entity\Menu $menu):array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.dishAllergens', 'da')
            ->leftJoin('da.dish', 'd')
            ->leftJoin('d.menuDishes', 'md')
            ->where('md.menu = :menu')
            ->setParameter('menu', $menu)
            ->getQuery()
            ->getResult();
    }
}

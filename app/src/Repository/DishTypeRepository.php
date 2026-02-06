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

//    /**
//     * @return DishType[] Returns an array of DishType objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DishType
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
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

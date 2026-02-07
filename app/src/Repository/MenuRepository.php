<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Menu>
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }


    public function findActiveMenus()
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.active = :active')
            ->setParameter('active', true)
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function search(array $filters): array
{
    $qb = $this->createQueryBuilder('m')
        ->leftJoin('m.theme', 't')->addSelect('t')
        ->leftJoin('m.diet', 'd')->addSelect('d');

    if ($filters['minPrice'] !== null && $filters['minPrice'] !== '') {
        $qb->andWhere('m.basePrice >= :minPrice')
            ->setParameter('minPrice', (int) $filters['minPrice']);
    }

    if ($filters['maxPrice'] !== null && $filters['maxPrice'] !== '') {
        $qb->andWhere('m.basePrice <= :maxPrice')
            ->setParameter('maxPrice', (int) $filters['maxPrice']);
    }

    if ($filters['theme'] !== null && $filters['theme'] !== '') {
        $qb->andWhere('t.id = :theme')
            ->setParameter('theme', (int) $filters['theme']);
    }

    if ($filters['diet'] !== null && $filters['diet'] !== '') {
        $qb->andWhere('d.id = :diet')
            ->setParameter('diet', (int) $filters['diet']);
    }

    if ($filters['minPersons'] !== null && $filters['minPersons'] !== '') {
        $qb->andWhere('m.minPeople <= :minPersons')
            ->setParameter('minPersons', (int) $filters['minPersons']);
    }

    if ($filters['stock'] !== null && $filters['stock'] !== '') {
        $qb->andWhere('m.stock >= :stock')
            ->setParameter('stock', (int) $filters['stock']);
    }

    if ($filters['isActive'] !== null && $filters['isActive'] !== '') {
        $qb->andWhere('m.active = :active')
            ->setParameter('active', filter_var($filters['isActive'], FILTER_VALIDATE_BOOLEAN));
    }

    return $qb->getQuery()->getResult();
}

}

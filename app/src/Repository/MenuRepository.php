<?php

namespace App\Repository;

use App\Dto\MenuApi\MenuApiFiltersDto;
use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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
            ->getResult();
    }

    public function searchPublic(MenuApiFiltersDto $filters): array
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.theme', 't')->addSelect('t')
            ->leftJoin('m.diet', 'd')->addSelect('d');

        $this->applyFilters($qb, $filters);
        $qb->andWhere('m.active = :active')
            ->setParameter('active', true);

        return $qb->getQuery()->getResult();
    }

    public function search(MenuApiFiltersDto $filters): array
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.theme', 't')->addSelect('t')
            ->leftJoin('m.diet', 'd')->addSelect('d');

        $this->applyFilters($qb, $filters);

        return $qb->getQuery()->getResult();
    }

    private function applyFilters(QueryBuilder $qb, MenuApiFiltersDto $filters): void
    {
        if (null !== $filters->minPrice) {
            $qb->andWhere('m.basePrice >= :minPrice')
                ->setParameter('minPrice', $filters->minPrice);
        }

        if (null !== $filters->maxPrice) {
            $qb->andWhere('m.basePrice <= :maxPrice')
                ->setParameter('maxPrice', $filters->maxPrice);
        }

        if (null !== $filters->theme) {
            $qb->andWhere('t.id = :theme')
                ->setParameter('theme', $filters->theme);
        }

        if (null !== $filters->diet) {
            $qb->andWhere('d.id = :diet')
                ->setParameter('diet', $filters->diet);
        }

        if (null !== $filters->minPersons) {
            $qb->andWhere('m.minPeople <= :minPersons')
                ->setParameter('minPersons', $filters->minPersons);
        }

        if (null !== $filters->stock) {
            $qb->andWhere('m.stock >= :stock')
                ->setParameter('stock', $filters->stock);
        }
    }
}

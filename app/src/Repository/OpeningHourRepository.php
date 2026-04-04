<?php

namespace App\Repository;

use App\Entity\OpeningHour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OpeningHour>
 */
class OpeningHourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OpeningHour::class);
    }

    public function openingTimerImformation()
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.isClosed = false')
            ->orderBy('o.dayOfWeek', 'ASC')
            ->addOrderBy('o.opensAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

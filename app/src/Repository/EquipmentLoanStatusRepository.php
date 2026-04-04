<?php

namespace App\Repository;

use App\Entity\EquipmentLoanStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EquipmentLoanStatus>
 */
class EquipmentLoanStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipmentLoanStatus::class);
    }
}

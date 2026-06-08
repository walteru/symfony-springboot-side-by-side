<?php

namespace App\Repository;

use App\Entity\SprintItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SprintItem>
 */
class SprintItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SprintItem::class);
    }

    /**
     * Orden estable por id para mantener la paridad visual con Spring Boot.
     *
     * @return SprintItem[]
     */
    public function findAllOrdered(): array
    {
        return $this->findBy([], ['id' => 'ASC']);
    }
}

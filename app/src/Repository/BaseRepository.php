<?php

namespace App\Repository;

use App\Entity\Tokens;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

abstract class BaseRepository extends ServiceEntityRepository
{
    private $qb;

    public function begin(): self
    {
        $this->qb = $this->createQueryBuilder('t');

        return $this;
    }

    public function getQb(): QueryBuilder
    {
        return $this->qb;
    }

    public function getResult()
    {
        return $this->getQb()
            ->getQuery()
            ->getResult()
        ;
    }

    public function getSingleResult()
    {
        return $this->getQb()
            ->getQuery()
            ->getSingleResult()
        ;
    }

    public function getOneOrNullResult()
    {
        return $this->getQb()
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}

<?php

namespace App\Repository;

use App\Entity\Tokens;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @TODO Придумать как передавать инстанс QueryBuilder для применения цепочки вызовов
 * @TODO Для избежания переопределения методов с использованием $this->getQb()
 * @TODO Можно попробовать использовать магические методы или рефлексию
 */
class TokensRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tokens::class);
    }

    public function findByNotExpiredDate($value): self
    {
        $this->getQb()
            ->andWhere('t.expired_at > :val')
            ->setParameter('val', $value)
        ;

        return $this;
    }

    public function findByToken($value): self
    {
        $this->getQb()
            ->andWhere('t.token = :token')
            ->setParameter('token', $value)
        ;

        return $this;
    }

    public function findByUser($value): self
    {
        $this->getQb()
            ->andWhere('t.user_id = :user')
            ->setParameter('user', $value)
        ;

        return $this;
    }
    // /**
    //  * @return Tokens[] Returns an array of Tokens objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tokens
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

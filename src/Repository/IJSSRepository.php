<?php

namespace App\Repository;

use App\Entity\IJSS;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method IJSS|null find($id, $lockMode = null, $lockVersion = null)
 * @method IJSS|null findOneBy(array $criteria, array $orderBy = null)
 * @method IJSS[]    findAll()
 * @method IJSS[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IJSSRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, IJSS::class);
    }

//    /**
//     * @return IJSS[] Returns an array of IJSS objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IJSS
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\Prolongation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Prolongation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prolongation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prolongation[]    findAll()
 * @method Prolongation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProlongationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Prolongation::class);
    }

//    /**
//     * @return Prolongation[] Returns an array of Prolongation objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Prolongation
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

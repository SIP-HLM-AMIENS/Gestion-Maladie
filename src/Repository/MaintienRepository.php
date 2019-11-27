<?php

namespace App\Repository;

use App\Entity\Maintien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Maintien|null find($id, $lockMode = null, $lockVersion = null)
 * @method Maintien|null findOneBy(array $criteria, array $orderBy = null)
 * @method Maintien[]    findAll()
 * @method Maintien[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaintienRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Maintien::class);
    }

    public function findLast($employe): ?Maintien
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.Employe = :employe')
            ->setParameter('employe', $employe)
            ->orderBy('m.DateCreation', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    /**
//     * @return Maintien[] Returns an array of Maintien objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Maintien
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

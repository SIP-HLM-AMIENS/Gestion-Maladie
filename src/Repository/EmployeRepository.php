<?php

namespace App\Repository;

use DateInterval;
use App\Entity\Employe;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Employe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employe[]    findAll()
 * @method Employe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Employe::class);
    }

//    /**
//     * @return Employe[] Returns an array of Employe objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Employe
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findArretBefore24($id): array
    {
        $date = new \DATETIME;
        $interval = new DateInterval('P2M');
        $date->sub($interval);


        $qb = $this->createQueryBuilder('e')
            ->select('a')
            ->join('App\Entity\Arret','a')
            ->where('e.id = :id')
            ->andWhere('a.DateOut >= :avant')
            ->setParameter('avant', $date)
            ->setParameter('id',$id)
            ->getQuery();

        return $qb->execute();


    }



    /*SELECT * 
FROM arret, employe
WHERE arret.employe_id = employe.id
AND arret.date_out >=  DATE_ADD(NOW(), INTERVAL -2 MONTH)*/
}

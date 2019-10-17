<?php

namespace App\Repository;

use DateInterval;
use App\Entity\Arret;
use App\Entity\Motif;
use App\Entity\Employe;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Arret|null find($id, $lockMode = null, $lockVersion = null)
 * @method Arret|null findOneBy(array $criteria, array $orderBy = null)
 * @method Arret[]    findAll()
 * @method Arret[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArretRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Arret::class);
    }

    public function findArretBy(string $year='0', string $month='0', Employe $employe = null, Motif $motif = null, array $etat = null): array
    {   
        $dql = "SELECT a from App\Entity\Arret a";
        $condition=false;
        if($year<>'0' && $month<>'0')
        {
            $dql .=" WHERE ((a.DateIn BETWEEN '$year-$month-01' AND '$year-$month-31') OR (a.DateOut BETWEEN '$year-$month-01' AND '$year-$month-31'))";
            $condition = true;
        }
        elseif($year<>'0')
        {
            $dql .=" WHERE ((a.DateIn BETWEEN '$year-01-01' AND '$year-12-31') OR (a.DateOut BETWEEN '$year-01-01' AND '$year-12-31')) ";
            $condition = true;
        }


        if($employe<>null)
        {
            if($condition)
            {
                $dql .=" AND a.employe = :employe ";
            }
            else
            {
                $dql .=" WHERE a.employe = :employe ";
                $condition =true;
            }
        }

        if($motif<>null)
        {
            if($condition)
            {
                $dql .=" AND a.motif = :motif ";
            }
            else
            {
                $dql .=" WHERE a.motif = :motif ";
                $condition= true;
            }
        }

        if($etat<>null)
        {
            if($condition)
            {
                $dql .=" AND a.clos in (:etats) ";
            }
            else
            {
                $dql .=" WHERE a.clos in (:etats) ";
                $condition= true;
            }
        }

        $query = $this->getEntityManager()->createQuery($dql);
        if($employe<>null)
        {
            $query = $query->setParameter('employe',$employe);
        }
        if($motif<>null)
        {
            $query = $query->setParameter('motif',$motif);
        }
        if($etat<>null)
        {
            $query = $query->setParameter('etats',$etat);
        }
        return $query->execute();
    }

    public function findOldArret(Employe $employe): array
    {
        $dql = "SELECT a from App\Entity\Arret a";
        $dql .= " WHERE a.employe = :employe";
        $query = $this->getEntityManager()->createQuery($dql);
        $query = $query->setParameter('employe',$employe);
        return $query->execute();
    }

    public function findArretBefore24($id, $date): array
    {
        //$date = new \DATETIME;
        $interval = new DateInterval('P2Y');
        $date->sub($interval);
        $dql = "SELECT a from App\Entity\Arret a ";
        $dql .= "JOIN a.employe e ";
        $dql .= "JOIN a.motif m ";
        $dql .= "WHERE e.id = :id AND a.DateOut >= :avant And m.Court = 'AM'";

        $query = $this->getEntityManager()->createQuery($dql);
        $query = $query->setParameter('id', $id);
        $query = $query->setParameter('avant', $date);
        return $query->execute();


    }
    public function findArretBefore24Prev($id, $date, $arret): array
    {
        //$date = new \DATETIME;
        $interval = new DateInterval('P2Y');
        $date->sub($interval);
        $dql = "SELECT a from App\Entity\Arret a ";
        $dql .= "JOIN a.employe e ";
        $dql .= "JOIN a.motif m ";
        $dql .= "WHERE e.id = :id AND a.DateOut >= :avant And m.Court = 'AM' And a.id <> :arret";

        $query = $this->getEntityManager()->createQuery($dql);
        $query = $query->setParameter('id', $id);
        $query = $query->setParameter('avant', $date);
        $query = $query->setParameter('arret', $arret);
        return $query->execute();

    }
    


//    /**
//     * @return Arret[] Returns an array of Arret objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Arret
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

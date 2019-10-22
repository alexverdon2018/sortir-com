<?php

namespace App\Repository;

use App\Entity\Rejoindre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Rejoindre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rejoindre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rejoindre[]    findAll()
 * @method Rejoindre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RejoindreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rejoindre::class);
    }

    // /**
    //  * @return Rejoindre[] Returns an array of Rejoindre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Rejoindre
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\Ste;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Ste|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ste|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ste[]    findAll()
 * @method Ste[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ste::class);
    }

    // /**
    //  * @return Ste[] Returns an array of Ste objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ste
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

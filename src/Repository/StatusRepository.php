<?php

namespace App\Repository;

use App\Entity\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Status>
 *
 * @method Status|null find($id, $lockMode = null, $lockVersion = null)
 * @method Status|null findOneBy(array $criteria, array $orderBy = null)
 * @method Status[]    findAll()
 * @method Status[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Status::class);
    }

    public function add(Status $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Status $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function allTripOpen()
    {
        $query = $this -> createQueryBuilder('s')
                       -> select('s.label')
                       -> andWhere('s.label = :label')
                       -> setParameter('label','Ouverte');

        return $query ->getQuery() -> getResult();
    }

    public function allTripClose()
    {
        $query = $this -> createQueryBuilder('s')
                       -> select('s.label')
                       -> andWhere('s.label = :label')
                       -> setParameter('label','Clôturée');

        return $query ->getQuery() -> getResult();
    }

    public function allTripNow()
    {
        $query = $this -> createQueryBuilder('s')
                       -> select('s.label')
                       -> andWhere('s.label = :label')
                       -> setParameter('label','Activité en cours');

        return $query ->getQuery() -> getResult();
    }

    public function allTripFail()
    {
        $query = $this -> createQueryBuilder('s')
                       -> select('s.label')
                       -> andWhere('s.label = :label')
                       -> setParameter('label','Annulée');

        return $query ->getQuery() -> getResult();
    }

    public function allTripFinish()
    {
        $query = $this -> createQueryBuilder('s')
                       -> select('s.label')
                       -> andWhere('s.label = :label')
                       -> setParameter('label','Activité passée');

        return $query ->getQuery() -> getResult();
    }



//    /**
//     * @return Status[] Returns an array of Status objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Status
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
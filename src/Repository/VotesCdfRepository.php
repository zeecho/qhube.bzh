<?php

namespace App\Repository;

use App\Entity\VotesCdf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VotesCdf>
 *
 * @method VotesCdf|null find($id, $lockMode = null, $lockVersion = null)
 * @method VotesCdf|null findOneBy(array $criteria, array $orderBy = null)
 * @method VotesCdf[]    findAll()
 * @method VotesCdf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VotesCdfRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VotesCdf::class);
    }

    public function save(VotesCdf $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(VotesCdf $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getVoteResults()
    {
        $results = [];

        $events = [
            'three' => '333',
            'two' => '222',
            'four' => '444',
            'five' => '555',
            'bld' => '333bf',
            'oh' => '333oh',
            'pyra' => 'pyram',
            'skewb' => 'skewb'
        ];

        foreach (['three', 'two', 'four', 'five', 'bld', 'oh', 'pyra', 'skewb'] as $event) {
            $votesForEvent = $this->createQueryBuilder('v')
                ->select('v.' . $event . ' as WCAID, COUNT(v) as nb_votes')
                ->groupBy('v.' . $event)
                ->orderBy('nb_votes', 'DESC')
                ->getQuery()
                ->getResult()
            ;
            $results[$events[$event]] = $votesForEvent;
        }

        return $results;
    }

//    /**
//     * @return VotesCdf[] Returns an array of VotesCdf objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?VotesCdf
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

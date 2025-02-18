<?php

namespace App\Repository;

use App\Entity\PeopleId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PeopleId>
 *
 * @method PeopleId|null find($id, $lockMode = null, $lockVersion = null)
 * @method PeopleId|null findOneBy(array $criteria, array $orderBy = null)
 * @method PeopleId[]    findAll()
 * @method PeopleId[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeopleIdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PeopleId::class);
    }

    public function save(PeopleId $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PeopleId $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function insertNewPeopleId(PeopleId $entity, bool $flush = false): void
    {
        $peopleIdCountries = $this->findBy(['wcaId' => $entity->getWcaId()]);
        $this->save($entity, $flush);
        if (count($peopleIdCountries) == 0) {
            $this->insertSpecificResultsOfOnePerson($entity->getWcaId());
        }
    }

    private function insertSpecificResultsOfOnePerson(string $wcaId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'INSERT INTO specific_results
                    (SELECT * 
                    FROM people_id p 
                    INNER JOIN Results r ON r.personId = p.wca_id
                    WHERE personId = :wca_id)';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':wca_id', $wcaId);

        return $stmt->executeQuery();
    }

//    /**
//     * @return PeopleId[] Returns an array of PeopleId objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PeopleId
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

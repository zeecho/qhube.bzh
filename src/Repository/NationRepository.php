<?php

namespace App\Repository;

use App\Entity\Nation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @extends ServiceEntityRepository<Nation>
 *
 * @method Nation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Nation|null findOneBy(array $criteria, array $orderBy = null)
// * @method Nation[]    findAll()
 * @method Nation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NationRepository extends ServiceEntityRepository
{
    private $translator;
    public function __construct(ManagerRegistry $registry, TranslatorInterface $translator)
    {
        parent::__construct($registry, Nation::class);
        $this->translator = $translator;
    }

    public function save(Nation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Nation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAll(): array
    {
        return $this->findBy(array(), array('name' => 'ASC'));
    }

    public function findAllOrderedByTranslations(): array
    {
        $countries = $this->findAll();
        $countriesChoices = [];
        foreach ($countries as $country) {
            $countryName = $this->translator->trans('rankings.country_names.' . $country->getShort(), [], 'messages');
            $countriesChoices[$countryName] = $country->getShort();
        }

        ksort($countriesChoices);
        return  $countriesChoices;
    }

    public function getResults($country, $event, $type)
    {
        if ($type == 'single') {
            $t = 'best';
        } else {
            $t = 'average';
            $type = 'average';
        }

        $sqlFunction = 'wca_statistics_time_format';
        if (in_array($event, ['333mbf', '333mbo'])) {
            $sqlFunction = 'wca_statistics_time_format_mbf';
        }

        $sql = 'SELECT a.personId,
                        ' . $sqlFunction . '(' . $t . ', :event, :type) as res,
                        competitionId,
                        personName,
                        ' . $sqlFunction . '(value1, :event, "single") AS value1,
                        ' . $sqlFunction . '(value2, :event, "single") AS value2,
                        ' . $sqlFunction . '(value3, :event, "single") AS value3,
                        ' . $sqlFunction . '(value4, :event, "single") AS value4,
                        ' . $sqlFunction . '(value5, :event, "single") AS value5
                    FROM (
                        SELECT personId, MIN(' . $t . ') AS lowest
                        FROM people_id p
                        INNER JOIN specific_results r ON r.personId = p.wca_id
                        WHERE p.country_short = :country
                          AND eventId = :event
                          AND ' . $t . ' > 0
                        GROUP BY personId) a
                        JOIN specific_results b  ON a.personId = b.personId
                        AND a.lowest = b.' . $t . '
                        WHERE eventId = :event
                        GROUP BY a.personId
                        ORDER BY ' . $t . ';';

        $conn = $this->getEntityManager()->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':event', $event);
        $stmt->bindValue(':type', $type);
        $stmt->bindValue(':country', $country);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }

//    /**
//     * @return Nation[] Returns an array of Nation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Nation
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

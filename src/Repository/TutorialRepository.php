<?php

namespace App\Repository;

use Doctrine\ORM\Query;
use App\Entity\Tutorial;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tutorial>
 *
 * @method Tutorial|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tutorial|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tutorial[]    findAll()
 * @method Tutorial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TutorialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tutorial::class);
    }

    public function save(Tutorial $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Tutorial $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findBySearch(SearchData $searchData): array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        if (!empty($searchData->query)) {
            $queryBuilder = $queryBuilder
                ->andWhere('p.name LIKE :query')
                ->setParameter('query', "%{$searchData->query}%");
        }
        $query = $queryBuilder->getQuery();
        $results = $query->getResult();

        return $results;
    }

    public function queryFindAll(): Query
    {
        return $this->createQueryBuilder('t')->getQuery();
    }

    public function findLikeName(string $search): Query
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.name LIKE :Rechercher_un_tutoriel')
            ->setParameter('Rechercher_un_tutoriel', '%' . $search . '%')
            ->getQuery();
    }
}

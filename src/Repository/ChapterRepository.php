<?php

namespace App\Repository;

use App\Entity\Chapter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chapter>
 */
class ChapterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chapter::class);
    }
    public function chaptersRead (int $limit, int $category): ?array{
        return $this->createQueryBuilder('c')
            ->leftJoin('s.categories', 'c')
            ->andWhere('c.id = :categoriesId')
            ->setParameter('categoriesId', $category)
            // ->leftJoin('s.usersFollow', 'uf') // je récupère les userFollow
            // ->leftJoin('s.usersLike', 'ul') // je récupère les userLike
            // ->groupBy('s.id') // je regroupe les histoire par les id
            ->getQuery() // je récupère la requete
            ->getResult(); // et je la transforme en résultat
    }
    //    /**
    //     * @return Chapter[] Returns an array of Chapter objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Chapter
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

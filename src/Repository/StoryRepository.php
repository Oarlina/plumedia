<?php

namespace App\Repository;

use App\Entity\Story;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Story>
 */
class StoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry){
        parent::__construct($registry, Story::class);
    }

    public function popularStory (int $limit): ?Story{
        return $this->createQueryBuilder('s');
        
    }

    // public function loadUserByIdentifier(string $identifier): ?User  {
    //     return $this->createQueryBuilder('u')
    //         ->andWhere('u.pseudo = :pseudo')
    //         ->setParameter('pseudo', $identifier)
    //         ->getQuery()
    //         ->getOneOrNullResult();
    // }

//    /**
//     * @return Story[] Returns an array of Story objects
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

//    public function findOneBySomeField($value): ?Story
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

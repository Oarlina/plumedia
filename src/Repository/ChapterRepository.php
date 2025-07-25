<?php
namespace App\Repository;
 
use App\Entity\User;
use App\Entity\Chapter;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
 
/**
 * @extends ServiceEntityRepository<Chapter>
 */
class ChapterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry){
        parent::__construct($registry, Chapter::class);
    }

    public function findChaptersReadByUser(User $user): array{
        return $this->createQueryBuilder('c')
            ->innerJoin('c.userHaveRead', 'u')
            ->where('u = :user')
            ->andWhere('c.isPublic = true')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findChaptersNotReadByUser(User $user): array{
        return $this->createQueryBuilder('c')
            ->select('DISTINCT c')
            ->join('c.story', 's')
            ->leftJoin('s.usersFollow', 'uf')
            ->leftJoin('s.usersLike', 'ul')
            ->leftJoin('c.userHaveRead', 'uhr')
            ->where('uhr IS NULL')
            ->andWhere('uf = :user or ul = :user')
            ->andWhere('c.isPublic = true')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return Chapter[] Returns an array of Chapter objects
    //     */
    //    public function findByExampleField($value): array{
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
    //    public function findOneBySomeField($value): ?Chapter{
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

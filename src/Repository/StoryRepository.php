<?php
namespace App\Repository;

use App\Entity\User;
use App\Entity\Story;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Story>
 */
class StoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry){
        parent::__construct($registry, Story::class);
    }

    public function nPopularStory (int $limit): ?array{
        return $this->createQueryBuilder('s')
            ->leftJoin('s.usersFollow', 'uf') // je récupère les userFollow
            ->leftJoin('s.usersLike', 'ul') // je récupère les userLike
            ->groupBy('s.id') // je regroupe les histoire par les id
            ->orderBy('COUNT(uf)', 'DESC') // je trie d'abord par rapport au follow
            ->orderBy('COUNT(ul)', 'DESC') // puis par les like s'il y a des follow égaux
            ->setMaxResults($limit) // et je donne la limite car dans l'accueil j'aurait une limite de 10 alors que populars en aura 5
            ->getQuery() // je récupère la requete
            ->getResult(); // et je la transforme en résultat
    }
    public function nPopularStoryCategories (int $limit, int $category): ?array{
        return $this->createQueryBuilder('s')
            ->leftJoin('s.categories', 'c')
            ->andWhere('c.id = :categoriesId')
            ->setParameter('categoriesId', $category)
            ->leftJoin('s.usersFollow', 'uf') // je récupère les userFollow
            ->leftJoin('s.usersLike', 'ul') // je récupère les userLike
            ->groupBy('s.id') // je regroupe les histoire par les id
            ->orderBy('COUNT(uf)', 'DESC') // je trie d'abord par rapport au follow
            ->orderBy('COUNT(ul)', 'DESC') // puis par les like s'il y a des follow égaux
            ->setMaxResults($limit) // et je donne la limite car dans l'accueil j'aurait une limite de 10 alors que populars en aura 5
            ->getQuery() // je récupère la requete
            ->getResult(); // et je la transforme en résultat
    }

    public function findAuthorMostPopular(User $user): ? array{
        return $this->createQueryBuilder('s')
            ->join('s.person', 'u')
            ->where('s.person = :user')
            ->leftJoin('s.usersFollow', 'uf') // je récupère les userFollow
            ->leftJoin('s.usersLike', 'ul') // je récupère les userLike
            ->groupBy('s.id')
            ->orderBy('COUNT(uf)', 'DESC') // je trie d'abord par rapport au follow
            ->orderBy('COUNT(ul)', 'DESC')
            ->setMaxResults(1)
            ->setParameter('user', $user)
            ->getQuery() // je récupère la requete
            ->getResult(); // et je la transforme en résultat;
    }

    public function findStoriesExcludingIds(array $idsToExclude): ?array{

        return $this->createQueryBuilder('s')
            ->where('s.id NOT IN (:ids)')
            ->setParameter('ids', $idsToExclude)
            ->setMaxResults(2)
            ->getQuery()
            ->getResult();
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

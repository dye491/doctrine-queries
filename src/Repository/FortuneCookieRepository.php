<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\FortuneCookie;
use App\Model\CategoryFortuneStats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FortuneCookie>
 *
 * @method FortuneCookie|null find($id, $lockMode = null, $lockVersion = null)
 * @method FortuneCookie|null findOneBy(array $criteria, array $orderBy = null)
 * @method FortuneCookie[]    findAll()
 * @method FortuneCookie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FortuneCookieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FortuneCookie::class);
    }

    public function save(FortuneCookie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FortuneCookie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countNumberPrintedForCategory(Category $category): CategoryFortuneStats
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT SUM(fortune_cookie.number_printed) AS fortunesPrinted, AVG(fortune_cookie.number_printed) AS fortunesAverage, category.name FROM fortune_cookie INNER JOIN category ON category.id = fortune_cookie.category_id WHERE fortune_cookie.category_id = :category_id';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('category_id', $category->getId());
        $result = $stmt->executeQuery([])->fetchAssociative();
        /*        return $this->createQueryBuilder('fortuneCookie')
                    ->select(sprintf(
                        'NEW %s(
                            SUM(fortuneCookie.numberPrinted),
                            AVG(fortuneCookie.numberPrinted),
                            category.name
                        )',
                        CategoryFortuneStats::class
                    ))
                    ->innerJoin('fortuneCookie.category', 'category')
                    ->andWhere('fortuneCookie.category = :category')
                    ->setParameter('category', $category)
                    ->getQuery()
                    ->getSingleResult();*/
        return new CategoryFortuneStats(...$result);
    }

//    /**
//     * @return FortuneCookie[] Returns an array of FortuneCookie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FortuneCookie
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

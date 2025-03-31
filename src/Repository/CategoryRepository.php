<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public static function createFortuneCookiesStillInProductionCriteria(): Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('discontinued', false));
    }

    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Category[] Returns an array of Category objects
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

//    public function findOneBySomeField($value): ?Category
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * @return Category[]
     */
    public function findAllOrdered(): array
    {
        return $this->addOrderByCategoryName(
            $this->createQueryBuilder('category')
//            ->addSelect('fortuneCookie')
//            ->leftJoin('category.fortuneCookies', 'fortuneCookie')
        )
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $term
     * @return Category[]
     */
    public function search(string $term): array
    {
        $termList = explode(' ', $term);

        return $this
            ->addOrderByCategoryName(
                $this->addFortuneCookieJoinAndSelect()
            )
            ->andWhere(
                (new Expr())->orX(
                    (new Expr())->like('category.name', ':searchTerm'),
                    (new Expr())->like('category.iconKey', ':searchTerm'),
                    (new Expr())->like('fortuneCookie.fortune', ':searchTerm'),
                    (new Expr())->in('category.name', ':termList')
                )
            )
//            ->andWhere('category.name LIKE :searchTerm OR category.iconKey LIKE :searchTerm OR category.name IN (:termList) OR fortuneCookie.fortune LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $term . '%')
            ->setParameter('termList', $termList)
            ->getQuery()
            ->getResult();
    }

    public function findWithFortuneJoin(int $id): ?Category
    {
        return $this->addFortuneCookieJoinAndSelect()
            ->andWhere('category.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function addFortuneCookieJoinAndSelect(QueryBuilder $qb = null): QueryBuilder
    {
        return ($qb ?? $this->createQueryBuilder('category'))
            ->addSelect('fortuneCookie')
            ->leftJoin('category.fortuneCookies', 'fortuneCookie');
    }

    private function addOrderByCategoryName(QueryBuilder $qb): QueryBuilder
    {
        return ($qb ?? $this->createQueryBuilder('category'))
            ->addOrderBy('category.name', Criteria::DESC);
    }
}

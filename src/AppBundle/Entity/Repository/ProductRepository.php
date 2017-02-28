<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Product;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

class ProductRepository extends EntityRepository
{
    /**
     * @param int $offset
     * @param null $shop
     * @param null $startDate
     * @param null $endDate
     * @return array
     */
    public function getTopDiscountProducts($offset = 0, $shop = null, $startDate = null, $endDate = null)
    {
        $qb = $this->createQueryBuilder('p1')
            ->select('p1 AS product')
            ->addSelect('(100 - (p1.onlinePrice * 100 / p1.oldPrice)) AS discountPercent')
            ->leftJoin(Product::class, 'p2', Join::WITH, 'p1.id < p2.id AND p1.externalId = p2.externalId ')
            ->where('p1.onlinePrice IS NOT NULL')
            ->andWhere('p1.oldPrice IS NOT NULL')
            ->andWhere('p1.onlinePrice < p1.oldPrice')
            ->andWhere('p2.id IS NULL')
            ->orderBy('discountPercent', 'DESC');

        if ($shop) {
            $qb->andWhere('p1.shop = :shop');
            $qb->setParameter('shop', $shop);
        }

        if ($startDate && $endDate) {
            $qb->andWhere('p1.createdAt BETWEEN :from AND :to')
                ->setParameter('from', new \DateTime($startDate))
                ->setParameter('to', new \DateTime($endDate));
        }

        return $qb->setFirstResult($offset)
            ->setMaxResults(15)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $query
     * @return array
     */
    public function searchDiscountProducts($query)
    {
        return $this->createQueryBuilder('p')
            ->where('p.title LIKE :query')
            ->groupBy('p.externalId')
            ->addGroupBy('p.shop')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $shop
     * @param $externalId
     * @return array
     */
    public function searchForProductChart($shop, $externalId)
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.shop = :shop')
            ->andWhere('p.externalId = :externalId')
            ->setParameter('shop', $shop)
            ->setParameter('externalId', $externalId)
            ->addOrderBy('p.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

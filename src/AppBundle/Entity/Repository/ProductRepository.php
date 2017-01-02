<?php

namespace AppBundle\Entity\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getTopDiscountProducts($offset = 0, $limit = 15)
    {
        $query = $this->getEntityManager()
            ->createQuery('
              SELECT
                p1 as product,
                (100 - (p1.onlinePrice * 100 / p1.oldPrice)) as discountPercent
              FROM AppBundle:Product p1
              LEFT JOIN AppBundle:Product p2 WITH p1.id < p2.id AND p1.externalId = p2.externalId 
              WHERE p1.onlinePrice is not null
                AND p1.oldPrice is not null
                AND p1.onlinePrice < p1.oldPrice
                AND p2.id IS NULL
              ORDER BY discountPercent DESC
            ')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $query->getResult();
    }

    /**
     * @param $query
     * @return array
     */
    public function searchDiscountProducts($query)
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.title like :query')
            ->groupBy('p.externalId')
            ->addGroupBy('p.shop')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param $shop
     * @param $externalId
     * @return array
     */
    public function searchForProductChart($shop, $externalId)
    {
        $query = $this->getEntityManager()
            ->createQuery('
              SELECT p
              FROM AppBundle:Product p
              WHERE p.shop = :shop
                AND p.externalId = :externalId
              ORDER BY p.createdAt ASC
            ')
            ->setParameter('shop', $shop)
            ->setParameter('externalId', $externalId);

        return $query->getResult();
    }
}

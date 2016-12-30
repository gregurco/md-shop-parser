<?php

namespace AppBundle\Entity\Repository;

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
        $query = $this->getEntityManager()
            ->createQuery('
              SELECT p
              FROM AppBundle:Product p
              WHERE p.title like :query
            ')
            ->setParameter('query', '%' . $query . '%');

        return $query->getResult();
    }
}

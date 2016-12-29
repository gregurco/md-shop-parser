<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getTopDiscountProducts()
    {
        $query = $this->getEntityManager()
            ->createQuery('
              SELECT
                p as product,
                (100 - (p.onlinePrice * 100 / p.oldPrice)) as discountPercent
              FROM AppBundle:Product p
              WHERE p.onlinePrice is not null
                AND p.oldPrice is not null
                AND p.onlinePrice < p.oldPrice
              ORDER BY discountPercent DESC
            ')
            ->setMaxResults(15);

        return $query->getResult();
    }
}

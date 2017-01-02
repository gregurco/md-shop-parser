<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Product;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('chartDatasets', array($this, 'chartDatasetsFilter')),
        );
    }

    /**
     * @param Product[] $productRecords
     * @return string
     */
    public function chartDatasetsFilter($productRecords)
    {
        $onlinePriceData = [];
        $specialPriceData = [];
        $oldPriceData = [];

        foreach ($productRecords as $product) {
            if ($product->getOnlinePrice()) {
                $onlinePriceData[] = ['x' => $product->getCreatedAt()->format('Y-m-d H:i:s'), 'y' => $product->getOnlinePrice()];
            }

            if ($product->getSpecialPrice()) {
                $specialPriceData[] = ['x' => $product->getCreatedAt()->format('Y-m-d H:i:s'), 'y' => $product->getSpecialPrice()];
            }

            if ($product->getOldPrice()) {
                $oldPriceData[] = ['x' => $product->getCreatedAt()->format('Y-m-d H:i:s'), 'y' => $product->getOldPrice()];
            }
        }

        return json_encode([
            ['label' => 'New price in online shop', 'data' => $onlinePriceData],
            ['label' => 'New price in retail shop', 'data' => $specialPriceData],
            ['label' => 'Old price', 'data' => $oldPriceData],
        ]);
    }

    public function getName()
    {
        return 'app_extension';
    }
}
<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->get('doctrine')->getRepository('AppBundle:Product');

        return $this->render('default/index.html.twig', [
            'topDiscountProducts' => $productRepository->getTopDiscountProducts(),
        ]);
    }

    /**
     * @Route("/load-more-discount-products", name="load_more_discount_products", options={"expose"=true})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadMoreDiscountProductsAction(Request $request)
    {
        $firstRecord = $request->get('firstRecord');
        $shop = $request->get('shop');
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');

        /** @var ProductRepository $productRepository */
        $productRepository = $this->get('doctrine')->getRepository('AppBundle:Product');

        return $this->render('default/_top_discount_rows.html.twig', [
            'topDiscountProducts' => $productRepository->getTopDiscountProducts($firstRecord, $shop, $startDate, $endDate),
        ]);
    }

    /**
     * @Route("/search-discount-products/{query}", name="search_discount_products", options={"expose"=true})
     *
     * @param $query
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchDiscountProductsAction($query)
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->get('doctrine')->getRepository('AppBundle:Product');

        return $this->json(['results' =>
            $this->get('serializer')->normalize($productRepository->searchDiscountProducts(urldecode($query)))]
        );
    }

    /**
     * @Route("/show-product-chart/{shop}/{externalId}", name="show_product_chart", options={"expose"=true})
     *
     * @param $shop
     * @param $externalId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showProductChartAction($shop, $externalId)
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->get('doctrine')->getRepository('AppBundle:Product');
        $productRecords = $productRepository->searchForProductChart($shop, $externalId);

        if (!count($productRecords)) {
            throw new NotFoundHttpException();
        }

        return $this->render('default/_product_chart.html.twig', [
            'productRecords'  => $productRecords,
        ]);
    }
}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->get('doctrine')->getRepository('AppBundle:Product');

        return $this->render('default/index.html.twig', [
            'topDiscountProducts' => $productRepository->getTopDiscountProducts(),
        ]);
    }

    /**
     * @Route("/load-more-discount-products/{firstRecord}", name="load_more_discount_products", options={"expose"=true})
     *
     * @param $firstRecord
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadMoreDiscountProductsAction($firstRecord)
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->get('doctrine')->getRepository('AppBundle:Product');

        return $this->render('default/_top_discount_rows.html.twig', [
            'topDiscountProducts' => $productRepository->getTopDiscountProducts($firstRecord),
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

        return $this->json(['results' => $this->get('serializer')->normalize($productRepository->searchDiscountProducts($query))]);
    }
}

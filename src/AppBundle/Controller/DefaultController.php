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
}

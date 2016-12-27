<?php

namespace AppBundle\Command;

use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Goutte\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ParseMaximumCommand extends ContainerAwareCommand
{
    protected $site = 'http://www.maximum.md';

    /** @var Client */
    protected $client;

    /** @var OutputInterface */
    protected $output;

    protected $productRepository;

    /** @var EntityManager */
    protected $em;

    protected function configure()
    {
        $this
            ->setName('app:parse:maximum')
            ->setDescription('Parse prices from maximum.md');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->client = new Client();
        $this->productRepository = $this->getContainer()->get('doctrine')->getRepository('AppBundle:Product');
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $this->doRequest($this->site)->filter('.navigation-item .header-item a')->each(function($category) {
            $categoriesCrawler = $this->doRequest($this->site . '/Catalog/CategoryNavigationBlock?parentCategoryId=' . $category->attr('data-cat-id') . '&_=' . time());

            $categoriesCrawler->filter('.sub-sub-link')->each(function($subCategory) {
                $page = 1;

                do {
                    $categoryLink = $this->site.$subCategory->attr('href').'?pagesize=60&pagenumber=' . $page;

                    $this->output->writeln([$categoryLink]);

                    $categoryCrawler = $this->doRequest($categoryLink);
                    $categoryCrawler->filter('.item-box')->each(function ($itemBox) {
                        $this->output->writeln([$itemBox->filter('.product-title a')->text()]);

                        $product = new Product();
                        $product->setShop('maximum');
                        $product->setTitle($itemBox->filter('.product-title a')->text());
                        $product->setLink($this->site . $itemBox->filter('.product-title a')->attr('href'));
                        $product->setOnlinePrice($itemBox->filter('.prices .online-price')->count() ? $itemBox->filter('.prices .online-price')->text() : null);
                        $product->setSpecialPrice($itemBox->filter('.prices .special-price')->count() ? $itemBox->filter('.prices .special-price')->text() : null);
                        $product->setOldPrice($itemBox->filter('.prices .old-price')->count() ? $itemBox->filter('.prices .old-price')->text() : null);

                        $this->em->persist($product);
                    });

                    $page++;
                    $this->em->flush();
                } while ($categoryCrawler->filter('.item-box')->count() !== 0);
            });
        });
    }

    /**
     * @param $url
     * @param string $type
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function doRequest($url, $type = 'GET')
    {
        sleep(5);

        return $this->client->request($type, $url);
    }
}
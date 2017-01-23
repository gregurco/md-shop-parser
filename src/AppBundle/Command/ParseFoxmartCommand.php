<?php

namespace AppBundle\Command;

use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Goutte\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ParseFoxmartCommand extends ContainerAwareCommand
{
    protected $site = 'http://www.foxmart.md';

    /** @var Client */
    protected $client;

    /** @var OutputInterface */
    protected $output;

    /** @var EntityManager */
    protected $em;

    protected $nextTimeRequest;

    protected function configure()
    {
        $this
            ->setName('app:parse:foxmart')
            ->setDescription('Parse prices from foxmart.md');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->client = new Client();
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $this->doRequest($this->site)->filter('.main_menu .menu_category a')->each(function($category) {
            // Get all items from each category
            $categoryCrawler = $this->doRequest($this->site . $category->attr('href') . '/1/999999');

            $categoryCrawler->filter('.products_block .product')->each(function ($itemBox) {
                $this->output->writeln([$itemBox->filter('h4 a')->text()]);

                $product = new Product();
                $product->setShop('foxmart');
                $product->setExternalId($itemBox->filter('.product-gid')->attr('value'));
                $product->setTitle(trim($itemBox->filter('h4 a')->text()));
                $product->setLink($this->site . $itemBox->filter('h4 a')->attr('href'));
                $product->setOnlinePrice(
                    $itemBox->filter('.price a')->count() ?
                        $this->preparePrice($itemBox->filter('.price a')->text()):
                        null
                );
                $product->setOldPrice(
                    $itemBox->filter('.product_view del')->count() ?
                        $this->preparePrice($itemBox->filter('.product_view del')->text()) :
                        null
                );

                $this->em->persist($product);
            });

            $this->em->flush();
        });
    }

    /**
     * @param $price
     * @return string
     */
    private function preparePrice($price)
    {
        return trim(str_replace('лей', '', $price));
    }

    /**
     * @param $url
     * @param string $type
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function doRequest($url, $type = 'GET')
    {
        sleep($this->nextTimeRequest > time() ? $this->nextTimeRequest - time() : 0);
        $this->nextTimeRequest = time() + 5;

        return $this->client->request($type, $url);
    }
}
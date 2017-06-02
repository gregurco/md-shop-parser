<?php

namespace AppBundle\Command;

use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Goutte\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ParseBombaCommand extends ContainerAwareCommand
{
    protected $site = 'http://www.bomba.md/ro';

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
            ->setName('app:parse:bomba')
            ->setDescription('Parse prices from bomba.md');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->client = new Client();
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $this->doRequest($this->site)->filter('.menu-list .cat-brand-link')->each(function($category) {
            $categoryLink = $this->site . '/collection-json/?cat_id=' . $category->attr('rel');

            $rawProducts = json_decode($this->doRequestJson($categoryLink));
            if ($rawProducts) {
                foreach ($rawProducts as $rawProduct) {
                    $this->output->writeln([$rawProduct->name]);

                    $product = new Product();
                    $product->setShop('bomba');
                    $product->setExternalId($rawProduct->id);
                    $product->setTitle($rawProduct->name);
                    $product->setLink($this->site . '/catalog/product/view/id/' . $rawProduct->id);
                    $product->setOnlinePrice($rawProduct->special_price ? $rawProduct->special_price : null);
                    $product->setSpecialPrice($rawProduct->retail_price_discount ? $rawProduct->retail_price_discount : null);
                    $product->setOldPrice($rawProduct->price ? $rawProduct->price : null);

                    $this->em->persist($product);
                }

                $this->em->flush();
            } else {
                $this->output->writeln(['Can\'t parse: ' . $categoryLink]);
            }
        });
    }

    /**
     * @param $url
     * @param string $type
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function doRequest($url, $type = 'GET')
    {
        $this->requestDelay();

        return $this->client->request($type, $url);
    }

    /**
     * @param $url
     * @return bool|string
     */
    protected function doRequestJson($url)
    {
        $this->requestDelay();

        return file_get_contents($url);
    }

    protected function requestDelay()
    {
        sleep($this->nextTimeRequest > time() ? $this->nextTimeRequest - time() : 0);
        $this->nextTimeRequest = time() + 5;
    }
}

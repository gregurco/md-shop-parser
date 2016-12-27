<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Goutte\Client;

class ParseMaximumCommand extends Command
{
    protected $site = 'http://www.maximum.md';

    /** @var Client */
    protected $client;

    /** @var OutputInterface */
    protected $output;

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

        $this->doRequest($this->site)->filter('.navigation-item .header-item a')->each(function($category) {
            $categoriesCrawler = $this->doRequest($this->site . '/Catalog/CategoryNavigationBlock?parentCategoryId=' . $category->attr('data-cat-id') . '&_=' . time());

            $categoriesCrawler->filter('.sub-sub-link')->each(function($subCategory) {
                $page = 1;

                do {
                    $categoryLink = $this->site.$subCategory->attr('href').'?pagesize=60&pagenumber=' . $page;

                    $this->output->writeln([$categoryLink]);

                    $categoryCrawler = $this->doRequest($categoryLink);
                    $categoryCrawler->filter('.item-box')->each(function ($itemBox) {
                        $this->output->writeln([
                            $itemBox->filter('.product-title a')->text(),
                            $itemBox->filter('.product-title a')->attr('href'),
                            $itemBox->filter('.prices .online-price')->count() ? $itemBox->filter('.prices .online-price')->text() : null,
                            $itemBox->filter('.prices .special-price')->count() ? $itemBox->filter('.prices .special-price')->text() : null,
                            $itemBox->filter('.prices .old-price')->count() ? $itemBox->filter('.prices .old-price')->text() : null,
                        ]);
                    });

                    $page++;
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
        sleep(1);

        return $this->client->request($type, $url);
    }
}
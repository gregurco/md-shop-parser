<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Goutte\Client;

class ParseMaximumCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('app:parse:maximum')
            ->setDescription('Parse prices from maximum.md');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client();
        $siteCrawler = $client->request('GET', 'http://www.maximum.md/ro/');

        $siteCrawler->filter('.navigation-item .header-item a')->each(function($category) use ($client, $output) {
            $categoryCrawler = $client->request('GET', 'http://www.maximum.md/Catalog/CategoryNavigationBlock?parentCategoryId=' . $category->attr('data-cat-id') . '&_=' . time());

            $categoryCrawler->filter('.sub-sub-link')->each(function($subCategory) use ($output) {
                $output->writeln(['http://www.maximum.md' . $subCategory->attr('href')]);
            });
        });
    }
}
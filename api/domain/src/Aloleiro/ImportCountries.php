<?php

namespace Muchacuba\Aloleiro;

use Goutte\Client;
use Muchacuba\Aloleiro\Country\ManageStorage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ImportCountries
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        ManageStorage $manageStorage
    )
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     */
    public function import()
    {
        (new Client())
            ->request('GET', 'http://www.aleida.net/paises-en.html')
            ->filter('table.sortable tbody tr')
            ->each(function(Crawler $crawler) {
                $crawler = $crawler->filter('td');

                $this->manageStorage->connect()->insertOne(new Country(
                    uniqid(),
                    str_replace("\n", '', $crawler->eq(0)->getNode(0)->textContent),
                    str_replace("\n", '', $crawler->eq(2)->getNode(0)->textContent)
                ));
            });
    }
}
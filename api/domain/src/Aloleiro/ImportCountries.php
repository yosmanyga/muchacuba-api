<?php

namespace Muchacuba\Aloleiro;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ImportCountries
{
    /**
     * @var AddCountry
     */
    private $addCountry;

    /**
     * @param AddCountry $addCountry
     */
    public function __construct(
        AddCountry $addCountry
    )
    {
        $this->addCountry = $addCountry;
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

                $this->addCountry->add(
                    str_replace("\n", '', $crawler->eq(0)->getNode(0)->textContent),
                    str_replace("\n", '', $crawler->eq(2)->getNode(0)->textContent),
                    null
                );
            });
    }
}
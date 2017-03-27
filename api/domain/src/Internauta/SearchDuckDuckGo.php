<?php

namespace Muchacuba\Internauta;

use Cubalider\Navigation\MarkComputer;
use Cubalider\Navigation\CreateClients;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class SearchDuckDuckGo
{
    /**
     * @var CreateClients
     */
    private $createClients;

    /**
     * @var MarkComputer
     */
    private $markComputer;

    /**
     * @param CreateClients $createClients
     * @param MarkComputer  $markComputer
     */
    public function __construct(
        CreateClients $createClients,
        MarkComputer $markComputer
    )
    {
        $this->createClients = $createClients;
        $this->markComputer = $markComputer;
    }

    /**
     * @param string $q
     *
     * @return array
     */
    public function search($q)
    {
        $clients = $this->createClients->create(100);

        $crawler = null;

        foreach ($clients as $id => $client) {
            try {
                $crawler = $client->request(
                    'GET',
                    'https://duckduckgo.com/lite'
                );
            } catch (\Exception $e) {
                $this->markComputer->markNotWorking($id);

                continue;
            }

            $this->markComputer->markWorking($id);

            break;
        }

        if (is_null($crawler)) {
            $client = $this->createClients->createDefault();
            $crawler = $client->request(
                'GET',
                'https://duckduckgo.com/lite'
            );
        }

        $form = $crawler->selectButton('Search')->form();
        $form['q'] = $q;
        $crawler = $client->submit($form);

        return $crawler
            ->filter('tr:not(.result-sponsored) a.result-link')
            ->each(function(Crawler $crawler) {
                return [
                    'link' => $crawler
                        ->filter('a.result-link')
                        ->attr('href')
                ];
            });
    }
}
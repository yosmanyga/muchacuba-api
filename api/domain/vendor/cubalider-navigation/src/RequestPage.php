<?php

namespace Cubalider\Navigation;

use Symfony\Component\DomCrawler\Crawler;

/**
 * @author Yosmany Garcia <yosmanyga@gmail.com>
 *
 * @di\service({
 *     deductible: true
 * })
 */
class RequestPage
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
     * @param string    $uri
     * @param bool|null $withProxy
     *
     * @return Crawler
     */
    public function request($uri, $withProxy = false)
    {
        if ($withProxy === true) {
            $clients = $this->createClients->create(100);

            foreach ($clients as $id => $client) {
                try {
                    $crawler = $client->request('GET', $uri);

                    if ($crawler->count() === 0) {
                        $this->markComputer->markNotWorking($id);

                        continue;
                    }

                    return $crawler;
                } catch (\Exception $e) {
                    $this->markComputer->markNotWorking($id);

                    continue;
                }
            }
        }

        return $this->createClients
            ->createDefault()
            ->request('GET', $uri);
    }
}
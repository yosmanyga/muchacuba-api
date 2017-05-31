<?php

namespace Muchacuba\Internauta\Advertising\Cubared;

use Muchacuba\Internauta\Advertising\Cubared\User\ManageStorage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service()
 */
class FetchUsers
{
    /**
     * @var LoginMyself
     */
    private $loginMyself;

    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param LoginMyself   $loginMyself
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        LoginMyself $loginMyself,
        ManageStorage $manageStorage
    )
    {
        $this->loginMyself = $loginMyself;
        $this->manageStorage = $manageStorage;
    }

    function fetch()
    {
        $client = $this->loginMyself->login();

        $crawler = $client->request(
            'GET',
            'http://www.cubared.com/?p=lista_usuarios'
        );

        $ids = $crawler
            ->filter('#todos_los_usuarios strong a')
            ->each(function(Crawler $crawler) {
                $href = $crawler->attr('href');

                return str_replace('./?p=muro&u=', '', $href);
            });
    }
}
<?php

namespace Cubalider\Navigation;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author Yosmany Garcia <yosmanyga@gmail.com>
 *
 * @di\service({
 *     private: true
 * })
 */
class ResolveAgents
{
    /**
     * @cli\resolution({command: "cubalider.navigation.resolve-agents"})
     *
     * @return array
     */
    public function resolve()
    {
        $agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.111 Safari/537.36';

        $client = new Client(['HTTP_USER_AGENT' => $agent]);
        $client->setHeader('User-Agent', $agent);
        $client->followRedirects(true);

        $agents = [];

        foreach ([
            'Chrome',
            'Mozilla',
            'Microsoft Edge',
            'Opera'
        ] as $browser) {
            $agents = array_merge($agents, $client
                ->request(
                    'GET',
                    sprintf('https://udger.com/resources/ua-list/browser-detail?browser=%s', urlencode($browser))
                )
                ->filter('table tr')
                ->eq(7)
                ->filter('p')
                ->each(function(Crawler $crawler) {
                    return $crawler->text();
                })
            );
        }

        return $agents;
    }
}

<?php

namespace Muchacuba\Internauta;

use Goutte\Client;

/**
 * @di\service({
 *     private: true
 * })
 */
class CreateClient
{
    /**
     * @return Client
     */
    public function create()
    {
        $agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.111 Safari/537.36';

        $client = new Client(['HTTP_USER_AGENT' => $agent]);
        $client->setHeader('User-Agent', $agent);
        $client->followRedirects(true);

        return $client;
    }
}
<?php

namespace Cubalider\Navigation;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

/**
 * @author Yosmany Garcia <yosmanyga@gmail.com>
 *
 * @di\service()
 */
class CreateClients
{
    /**
     * @var PickComputer
     */
    private $pickComputer;

    /**
     * @param PickComputer $pickComputer
     */
    public function __construct(PickComputer $pickComputer)
    {
        $this->pickComputer = $pickComputer;
    }

    /**
     * @param int       $amount
     *
     * @return Client[]
     */
    public function create($amount)
    {
        $unknownComputer = $this->pickComputer->pickUnknown(ceil($amount / 2));
        $workingComputer = $this->pickComputer->pickWorking($amount - (ceil($amount / 2)));
        /** @var Computer[] $computers */
        $computers = array_merge($unknownComputer, $workingComputer);

        $clients = [];
        foreach ($computers as $computer) {
            $client = new Client();
            $client->setClient(new GuzzleClient([
                'HTTP_USER_AGENT' => $computer->getAgent(),
                'proxy' => [
                    $computer->getProtocol() => sprintf(
                        '%s:%s',
                        $computer->getIp(),
                        $computer->getPort()
                    )
                ],
                'curl' => [
                    CURLOPT_TIMEOUT, 20
                ]
            ]));
            $client->setHeader('User-Agent', $computer->getAgent());
            $client->followRedirects(true);

            $clients[$computer->getId()] = $client;
        }

        return $clients;
    }

    /**
     * @return Client
     */
    public function createDefault()
    {
        $agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.111 Safari/537.36';
        $client = new Client([
            'HTTP_USER_AGENT' => $agent
        ]);
        $client->setHeader('User-Agent', $agent);
        $client->followRedirects(true);

        return $client;
    }
}

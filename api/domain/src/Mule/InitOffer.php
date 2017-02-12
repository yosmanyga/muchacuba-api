<?php

namespace Muchacuba\Mule;

use AlgoliaSearch\AlgoliaException;
use AlgoliaSearch\Client;
use AlgoliaSearch\Index;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class InitOffer
{
    /**
     * @var string
     */
    private $application;

    /**
     * @var string
     */
    private $api;

    /**
     * @var string
     */
    private $index;

    /**
     * @var string
     */
    private $proxy;

    /**
     * @param string $application
     * @param string $api
     * @param string $index
     * @param string $proxy
     *
     * @di\arguments({
     *    application: "%algolia_application%",
     *    api:         "%algolia_api%",
     *    index:       "%algolia_index%",
     *    proxy:       "%proxy%"
     * })
     */
    public function __construct(
        $application,
        $api,
        $index,
        $proxy
    ) {
        $this->application = $application;
        $this->api = $api;
        $this->index = $index;
        $this->proxy = $proxy;
    }

    /**
     * @throws AlgoliaException
     *
     * @return Index
     */
    public function init()
    {
        $options = [];

        if ($this->proxy != '') {
            $pieces = parse_url($this->proxy);

            $options['curloptions'] = [
                'CURLOPT_PROXY' => $pieces['host'],
                'CURLOPT_PROXYPORT' => $pieces['port'],
                'CURLOPT_PROXYUSERPWD' => sprintf('%s:%s', $pieces['user'], $pieces['pass']),
            ];
        }

        $client = new Client($this->application, $this->api, null, $options);

        return $client->initIndex($this->index);
    }
}

<?php

namespace Muchacuba\Chuchuchu\User;

use AlgoliaSearch\AlgoliaException;
use AlgoliaSearch\Client;
use AlgoliaSearch\Index;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class InitAlgoliaIndex
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
     * @param string $application
     * @param string $api
     * @param string $index
     *
     * @di\arguments({
     *    application: "%algolia_application%",
     *    api:         "%algolia_api%",
     *    index:       "%algolia_user_index%",
     * })
     */
    public function __construct(
        $application,
        $api,
        $index
    ) {
        $this->application = $application;
        $this->api = $api;
        $this->index = $index;
    }

    /**
     * @throws AlgoliaException
     *
     * @return Index
     */
    public function init()
    {
        $options = [];

        $client = new Client($this->application, $this->api, null, $options);

        return $client->initIndex($this->index);
    }
}

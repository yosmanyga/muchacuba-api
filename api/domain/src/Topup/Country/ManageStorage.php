<?php

namespace Muchacuba\Topup\Country;

use Cubalider\PurgeStorage;
use MongoDB\Client;
use MongoDB\Collection;

/**
 * @di\service({
 *     private: true,
 *     tags: ['muchacuba.aloleiro.purge_storage']
 * })
 */
class ManageStorage implements PurgeStorage
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $port;

    /**
     * @var string
     */
    private $db;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @param string $host
     * @param string $port
     * @param string $db
     *
     * @di\arguments({
     *     host: "%mongo_host%",
     *     port: "%mongo_port%",
     *     db:   "%mongo_db%"
     * })
     */
    public function __construct($host, $port, $db)
    {
        $this->host = $host;
        $this->port = $port;
        $this->db = $db;
    }

    /**
     * Connects to mongodb.
     *
     * @return Collection
     */
    public function connect()
    {
        if (!$this->collection) {
            $this->collection =
                (new Client(
                    sprintf('mongodb://%s:%s', $this->host, $this->port),
                    [],
                    [
                        'typeMap' => [
                            'array' => 'array',
                            'document' => 'array',
                        ],
                    ]
                ))
                    ->selectCollection(
                        $this->db,
                        'topup_countries'
                    );
        }

        return $this->collection;
    }

    /**
     * Prepares storage.
     */
    public function prepare()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        $this->connect()->drop();
    }
}

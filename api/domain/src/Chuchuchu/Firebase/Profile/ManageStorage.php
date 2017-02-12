<?php

namespace Muchacuba\Chuchuchu\Firebase\Profile;

use MongoDB\Client;
use MongoDB\Collection;

/**
 * @di\service()
 */
class ManageStorage
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
                        'chuchuchu_firebase_profiles'
                    );
        }

        return $this->collection;
    }

    /**
     * Purges storage.
     */
    public function purge()
    {
        $this->connect()->drop();
    }
}

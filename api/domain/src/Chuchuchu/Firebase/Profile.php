<?php

namespace Muchacuba\Chuchuchu\Firebase;

use MongoDB\BSON\Persistable;

class Profile implements Persistable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $token;

    /**
     * @param string $id
     * @param string $token
     */
    public function __construct(
        $id,
        $token
    ) {
        $this->id = $id;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'token' => $this->token,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->token = $data['token'];
    }
}

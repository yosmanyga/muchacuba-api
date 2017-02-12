<?php

namespace Muchacuba\Internauta\Advertising;

use MongoDB\BSON\Persistable;

class Profile implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $email;
    
    /**
     * @var Advertisement[]
     */
    private $advertisements;

    /**
     * @param string               $user
     * @param string               $email
     * @param Advertisement[]|null $advertisements
     */
    public function __construct(
        $user,
        $email,
        $advertisements = null
    ) {
        $this->user = $user;
        $this->email = $email;
        $this->advertisements = $advertisements ?: [];
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return Advertisement[]
     */
    public function getAdvertisements()
    {
        return $this->advertisements;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->user,
            'email' => $this->email,
            'advertisements' => $this->advertisements,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->user = $data['_id'];
        $this->email = $data['email'];
        $this->advertisements = $data['advertisements'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'user' => $this->user,
            'email' => $this->email,
            'advertisements' => $this->advertisements,
        ];
    }
}

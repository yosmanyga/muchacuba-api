<?php

namespace Muchacuba\Internauta\Importing;

use MongoDB\BSON\Persistable;

class User implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string|null
     */
    private $mobile;

    /**
     * @param string      $id
     * @param string      $email
     * @param string|null $mobile
     */
    public function __construct($id, $email, $mobile = null)
    {
        $this->id = $id;
        $this->email = $email;
        $this->mobile = $mobile;
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'email' => $this->email,
            'mobile' => $this->mobile
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $mobile)
    {
        $this->id = $mobile['_id'];
        $this->email = $mobile['email'];
        $this->mobile = $mobile['mobile'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'mobile' => $this->mobile
        ];
    }
}

<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\Persistable;

class Approval implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $business;

    /**
     * @var string[]
     */
    private $roles;

    /**
     * @param string   $email
     * @param string   $business
     * @param string[] $roles
     */
    public function __construct(
        $email,
        $business,
        $roles
    ) {
        $this->email = $email;
        $this->business = $business;
        $this->roles = $roles;
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
    public function getBusiness()
    {
        return $this->business;
    }

    /**
     * @return string[]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->email,
            'business' => $this->business,
            'roles' => $this->roles
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->email = $data['_id'];
        $this->business = $data['business'];
        $this->roles = $data['roles'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'email' => $this->email,
            'business' => $this->business,
            'roles' => $this->roles,
        ];
    }
}

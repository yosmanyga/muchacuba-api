<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\Persistable;

class AdminApproval implements Persistable
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $role;

    /**
     * @param string $email
     * @param string $role
     */
    public function __construct(
        $email,
        $role
    ) {
        $this->email = $email;
        $this->role = $role;
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
    public function getRole()
    {
        return $this->role;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->email,
            'role' => $this->role
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->email = $data['_id'];
        $this->role = $data['role'];
    }
}

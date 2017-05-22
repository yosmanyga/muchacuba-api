<?php

namespace Cubalider\Facebook;

use MongoDB\BSON\Persistable;

/**
 * @author Yosmany Garcia <yosmanyga@gmail.com>
 */
class Profile implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $uniqueness;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $picture;

    /**
     * @param string $uniqueness
     * @param string $id
     * @param string $name
     * @param string $email
     * @param string $picture
     */
    public function __construct($uniqueness, $id, $name, $email, $picture)
    {
        $this->uniqueness = $uniqueness;
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->picture = $picture;
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
    public function getUniqueness()
    {
        return $this->uniqueness;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->uniqueness,
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'picture' => $this->picture,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->uniqueness = $data['_id'];
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->picture = $data['picture'];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'uniqueness' => $this->uniqueness,
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'picture' => $this->picture,
        ];
    }
}

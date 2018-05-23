<?php

namespace Muchacuba\Internauta\Advertising;

use MongoDB\BSON\Persistable;

class Advertisement implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var int
     */
    private $date;

    /**
     * @param string $email
     * @param int    $date
     */
    public function __construct(
        $email,
        $date
    ) {
        $this->email = $email;
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            'email' => $this->email,
            'date' => $this->date
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->email = $data['email'];
        $this->date = $data['date'];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'email' => $this->email,
            'date' => $this->date
        ];
    }
}

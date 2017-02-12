<?php

namespace Muchacuba;

class User implements \JsonSerializable
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
     * @var string
     */
    private $mobile;

    /**
     * @param string $id
     * @param string $email
     * @param string $mobile
     */
    public function __construct($id, $email, $mobile)
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
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'mobile' => $this->mobile,
        ];
    }
}

<?php

namespace Muchacuba\Topup;

use MongoDB\BSON\Persistable;

class Provider implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $logo;

    /**
     * @var string
     */
    private $validation;

    /**
     * @param string $id
     * @param string $country
     * @param string $name
     * @param string $logo
     * @param string $validation
     */
    public function __construct(
        $id,
        $country,
        $name,
        $logo,
        $validation
    ) {
        $this->id = $id;
        $this->country = $country;
        $this->name = $name;
        $this->logo = $logo;
        $this->validation = $validation;
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
    public function getCountry()
    {
        return $this->country;
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
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @return string
     */
    public function getValidation()
    {
        return $this->validation;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'country' => $this->country,
            'name' => $this->name,
            'logo' => $this->logo,
            'validation' => $this->validation
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->country = $data['country'];
        $this->name = $data['name'];
        $this->logo = $data['logo'];
        $this->validation = $data['validation'];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'country' => $this->country,
            'name' => $this->name,
            'logo' => $this->logo,
            'validation' => $this->validation
        ];
    }
}

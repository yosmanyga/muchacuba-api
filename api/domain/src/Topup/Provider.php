<?php

namespace Muchacuba\Topup;

use MongoDB\BSON\Persistable;

class Provider implements Persistable, \JsonSerializable
{
    const TYPE_PHONE = 'phone';
    const TYPE_EMAIL = 'email';

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
     * @var string
     */
    private $type;

    /**
     * @param string $id
     * @param string $country
     * @param string $name
     * @param string $logo
     * @param string $validation
     * @param string $type
     */
    public function __construct(
        $id,
        $country,
        $name,
        $logo,
        $validation,
        $type
    ) {
        $this->id = $id;
        $this->country = $country;
        $this->name = $name;
        $this->logo = $logo;
        $this->validation = $validation;
        $this->type = $type;
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
            'validation' => $this->validation,
            'type' => $this->type
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
        $this->type = $data['type'];
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
            'validation' => $this->validation,
            'type' => $this->type
        ];
    }
}

<?php

namespace Muchacuba\Topup;

use MongoDB\BSON\Persistable;

class Product implements Persistable, \JsonSerializable
{
    /**
     * Sku Code
     * 
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $provider;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $logo;

    /**
     * @param string $id
     * @param string $provider
     * @param string $name
     * @param string $logo
     */
    public function __construct(
        $id,
        $provider,
        $name,
        $logo
    ) {
        $this->id = $id;
        $this->provider = $provider;
        $this->name = $name;
        $this->logo = $logo;
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
    public function getProvider()
    {
        return $this->provider;
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
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'provider' => $this->provider,
            'name' => $this->name,
            'logo' => $this->logo
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->provider = $data['provider'];
        $this->name = $data['name'];
        $this->logo = $data['logo'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'provider' => $this->provider,
            'name' => $this->name,
            'logo' => $this->logo,
        ];
    }
}

<?php

namespace Muchacuba\Topup;

use MongoDB\BSON\Persistable;

class Product implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $provider;

    /**
     * @var string
     */
    private $description;

    /**
     * @param string $code
     * @param string $value
     * @param string $provider
     * @param string $description
     */
    public function __construct(
        $code,
        $value,
        $provider,
        $description
    ) {
        $this->code = $code;
        $this->value = $value;
        $this->provider = $provider;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            'code' => $this->code,
            'value' => $this->value,
            'provider' => $this->provider,
            'description' => $this->description
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->code = $data['code'];
        $this->value = $data['value'];
        $this->provider = $data['provider'];
        $this->description = $data['description'];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'code' => $this->code,
            'value' => $this->value,
            'provider' => $this->provider,
            'description' => $this->description
        ];
    }
}

<?php

namespace Muchacuba\Topup;

use MongoDB\BSON\Persistable;

class Payload implements Persistable, \JsonSerializable
{
    const TYPE_PROVIDER = 'provider';
    const TYPE_PROVIDER_LOGO = 'product-logo';
    const TYPE_PRODUCT = 'product';
    const TYPE_PRODUCT_DESCRIPTION = 'product-description';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $data;

    /**
     * @param string $id
     * @param string $type
     * @param array  $data
     */
    public function __construct(
        $id,
        $type,
        array $data
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->data = $data;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'type' => $this->type,
            'data' => $this->data
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->type = $data['type'];
        $this->data = $data['data'];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'data' => $this->data,
        ];
    }
}

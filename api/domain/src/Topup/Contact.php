<?php

namespace Muchacuba\Topup;

use MongoDB\BSON\Persistable;

class Contact implements Persistable, \JsonSerializable
{
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
    private $recipient;

    /**
     * Cubacel,
     * Nauta,
     * Etecsa fijo factura,
     * Etecsa fijo mi cuenta prepago
     *
     * @var string
     */
    private $service;

    /**
     * @param string $id
     * @param string $name
     * @param string $recipient
     * @param string $service
     */
    public function __construct(
        $id,
        $name,
        $recipient,
        $service
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->recipient = $recipient;
        $this->service = $service;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'name' => $this->name,
            'recipient' => $this->recipient,
            'service' => $this->service
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->name = $data['name'];
        $this->recipient = $data['recipient'];
        $this->service = $data['service'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'recipient' => $this->recipient,
            'service' => $this->service,
        ];
    }
}

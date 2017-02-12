<?php

namespace Muchacuba;

use MongoDB\BSON\Persistable;

class Feedback implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $text;

    /**
     * @param string   $id
     * @param string   $text
     */
    public function __construct(
        $id,
        $text
    ) {
        $this->id = $id;
        $this->text = $text;
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
    public function getText()
    {
        return $this->text;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'text' => $this->text,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->text = $data['text'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
        ];
    }
}

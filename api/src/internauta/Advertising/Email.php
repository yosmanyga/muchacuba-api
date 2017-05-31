<?php

namespace Muchacuba\Internauta\Advertising;

use MongoDB\BSON\Persistable;

class Email implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $body;

    /**
     * @param string        $id
     * @param string        $subject
     * @param string        $body
     */
    public function __construct(
        $id,
        $subject,
        $body
    ) {
        $this->id = $id;
        $this->subject = $subject;
        $this->body = $body;
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
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'subject' => $this->subject,
            'body' => $this->body
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->subject = $data['subject'];
        $this->body = $data['body'];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'body' => $this->body
        ];
    }
}

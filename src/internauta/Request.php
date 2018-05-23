<?php

namespace Muchacuba\Internauta;

use MongoDB\BSON\Persistable;

class Request implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $body;

    /**
     * @var string[]
     */
    private $attachments;

    /**
     * @param string        $id
     * @param string        $from
     * @param string        $to
     * @param string        $subject
     * @param string        $body
     * @param string[]|null $attachments
     */
    public function __construct(
        $id,
        $from,
        $to,
        $subject,
        $body,
        $attachments = []
    ) {
        $this->id = $id;
        $this->from = $from;
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
        $this->attachments = $attachments;
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
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
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
     * @return string[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
            'body' => $this->body,
            'attachments' => $this->attachments
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->from = $data['from'];
        $this->to = $data['to'];
        $this->subject = $data['subject'];
        $this->body = $data['body'];
        $this->attachments = $data['attachments'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
            'body' => $this->body,
            'attachments' => $this->attachments
        ];
    }
}

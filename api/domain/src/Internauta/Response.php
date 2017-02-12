<?php

namespace Muchacuba\Internauta;

class Response implements \JsonSerializable
{
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
     * @param string        $from
     * @param string        $to
     * @param string        $subject
     * @param string        $body
     * @param string[]|null $attachments
     */
    public function __construct(
        $from,
        $to,
        $subject,
        $body,
        $attachments = []
    ) {
        $this->from = $from;
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
        $this->attachments = $attachments;
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
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
            'body' => $this->body,
            'attachments' => $this->attachments
        ];
    }
}

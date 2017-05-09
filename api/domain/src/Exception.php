<?php

namespace Muchacuba;

use MongoDB\BSON\Persistable;

class Exception implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $file;

    /**
     * @var int
     */
    private $line;

    /**
     * @param string $id
     * @param string $message
     * @param string $code
     * @param string $file
     * @param int    $line
     */
    public function __construct(
        $id,
        $message,
        $code,
        $file,
        $line
    )
    {
        $this->id = $id;
        $this->message = $message;
        $this->code = $code;
        $this->file = $file;
        $this->line = $line;
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
    public function getMessage()
    {
        return $this->message;
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
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'message' => $this->message,
            'code' => $this->code,
            'file' => $this->file,
            'line' => $this->line
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->message = $data['message'];
        $this->code = $data['code'];
        $this->file = $data['file'];
        $this->line = $data['line'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'file' => $this->file,
            'line' => $this->line
        ];
    }
}

<?php

namespace Muchacuba\Chuchuchu;

use MongoDB\BSON\Persistable;

class Message implements Persistable, \JsonSerializable
{
    const MIME_TEXT = 't';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $conversation;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $mime;

    /**
     * @var int
     */
    private $date;

    /**
     * @param string $id
     * @param string $conversation
     * @param string $user
     * @param string $content
     * @param string $mime
     * @param int $date
     */
    public function __construct(
        $id,
        $conversation,
        $user,
        $content,
        $mime,
        $date
    )
    {
        $this->id = $id;
        $this->conversation = $conversation;
        $this->user = $user;
        $this->content = $content;
        $this->mime = $mime;
        $this->date = $date;
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
    public function getConversation()
    {
        return $this->conversation;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * @return int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'conversation' => $this->conversation,
            'user' => $this->user,
            'content' => $this->content,
            'mime' => $this->mime,
            'date' => $this->date
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->conversation = $data['conversation'];
        $this->user = $data['user'];
        $this->content = $data['content'];
        $this->mime = $data['mime'];
        $this->date = $data['date'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'conversation' => $this->conversation,
            'user' => $this->user,
            'content' => $this->content,
            'mime' => $this->mime,
            'date' => $this->date
        ];
    }
}

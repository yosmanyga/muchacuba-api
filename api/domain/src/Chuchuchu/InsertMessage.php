<?php

namespace Muchacuba\Chuchuchu;

use Muchacuba\Chuchuchu\Message\ManageStorage;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class InsertMessage
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        ManageStorage $manageStorage
    ) {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param string   $conversation
     * @param string   $user
     * @param string   $content
     * @param string   $mime
     * @param int|null $date
     *
     * @return string
     */
    public function insert($conversation, $user, $content, $mime, $date = null)
    {
        // TODO: Validate that user is inside the conversation

        $id = uniqid();

        $date = $date ?: time();

        $this->manageStorage->connect()->insertOne(new Message(
            $id,
            $conversation,
            $user,
            $content,
            $mime,
            $date
        ));

        return $id;
    }
}
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
     * @var NotifyUsers
     */
    private $notifyUsers;

    /**
     * @param ManageStorage $manageStorage
     * @param NotifyUsers   $notifyUsers
     */
    public function __construct(
        ManageStorage $manageStorage,
        NotifyUsers $notifyUsers
    ) {
        $this->manageStorage = $manageStorage;
        $this->notifyUsers = $notifyUsers;
    }

    /**
     * @param string $conversation
     * @param string $user
     * @param string $content
     *
     * @return string
     */
    public function insert($conversation, $user, $content)
    {
        // TODO: Validate that user is inside the conversation

        $id = uniqid();

        $date = time();

        $this->manageStorage->connect()->insertOne(new Message(
            $id,
            $conversation,
            $user,
            $content,
            Message::MIME_TEXT,
            $date
        ));

        $this->notifyUsers->notify(
            $conversation,
            $user,
            $content,
            $date
        );

        return $id;
    }
}
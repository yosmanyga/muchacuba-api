<?php

namespace Muchacuba\Chuchuchu;

use Muchacuba\Chuchuchu\Conversation\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class InitConversation
{
    /**
     * @var PickConversation
     */
    private $pickConversation;

    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var InsertMessage
     */
    private $insertMessage;

    /**
     * @var NotifyUser
     */
    private $notifyUser;

    /**
     * @param PickConversation $pickConversation
     * @param ManageStorage    $manageStorage
     * @param InsertMessage    $insertMessage
     * @param NotifyUser       $notifyUser
     */
    public function __construct(
        PickConversation $pickConversation,
        ManageStorage $manageStorage,
        InsertMessage $insertMessage,
        NotifyUser $notifyUser
    )
    {
        $this->pickConversation = $pickConversation;
        $this->manageStorage = $manageStorage;
        $this->insertMessage = $insertMessage;
        $this->notifyUser = $notifyUser;
    }

    /**
     * @param string   $sender
     * @param string[] $recipients
     * @param array    $messages
     *
     * @return string
     */
    public function init($sender, $recipients, $messages)
    {
        try {
            $id = $this->pickConversation
                ->pick(null, array_merge([$sender], $recipients))
                ->getId();
        } catch (NonExistentConversationException $e) {
            $id = uniqid();

            $this->manageStorage->connect()->insertOne(new Conversation(
                $id,
                array_merge([$sender], $recipients)
            ));
        }

        $date = time();

        foreach ($messages as $message) {
            $this->insertMessage->insert(
                $id,
                $sender,
                $message['content'],
                $message['mime'],
                $date
            );
        }

        foreach ($recipients as $recipient) {
            $this->notifyUser->notify(
                $id,
                $sender,
                $recipient,
                "Tienes nuevos mensajes",
                $date
            );
        }

        return $id;
    }
}
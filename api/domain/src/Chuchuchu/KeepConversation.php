<?php

namespace Muchacuba\Chuchuchu;

use Muchacuba\Chuchuchu\Conversation\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class KeepConversation
{
    /**
     * @var PickConversation
     */
    private $pickConversation;

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
     * @param InsertMessage    $insertMessage
     * @param NotifyUser       $notifyUser
     */
    public function __construct(
        PickConversation $pickConversation,
        InsertMessage $insertMessage,
        NotifyUser $notifyUser
    )
    {
        $this->pickConversation = $pickConversation;
        $this->insertMessage = $insertMessage;
        $this->notifyUser = $notifyUser;
    }

    /**
     * @param string $uniqueness
     * @param string $conversation
     * @param array  $messages
     */
    public function keep($uniqueness, $conversation, $messages)
    {
        if (empty($messages)) {
            // TODO
        }

        // Get conversation data
        // Also ensures that author belongs to the conversation
        $conversation = $this->pickConversation->pick($conversation, [$uniqueness]);

        $date = time();

        foreach ($messages as $message) {
            $this->insertMessage->insert(
                $conversation->getId(),
                $uniqueness,
                $message['content'],
                $message['mime'],
                $date
            );
        }

        $count = count($messages);
        if ($count == 1) {
            // Custom notification if was only one message
            if ($messages[0]['mime'] == Message::MIME_TEXT) {
                // Use content to build notification message
                $notificationMessage = $messages[0]['content'];
            } else {
                // TODO
                // Photo, video, audio
                $notificationMessage = 'Mensaje nuevo';
            }
        } else {
            $notificationMessage = sprintf('%s mensajes nuevos', $count);
        }

        foreach ($conversation->getParticipants() as $participant) {
            $this->notifyUser->notify(
                $conversation->getId(),
                $uniqueness,
                $participant,
                $notificationMessage,
                $date
            );
        }
    }
}
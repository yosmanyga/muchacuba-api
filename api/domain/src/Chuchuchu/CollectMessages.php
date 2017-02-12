<?php

namespace Muchacuba\Chuchuchu;

use Muchacuba\Chuchuchu\Message\ManageStorage as ManageMessageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectMessages
{
    /**
     * @var PickConversation
     */
    private $pickConversation;

    /**
     * @var ManageMessageStorage
     */
    private $manageMessageStorage;

    /**
     * @param PickConversation     $pickConversation
     * @param ManageMessageStorage $manageMessageStorage
     */
    public function __construct(
        PickConversation $pickConversation,
        ManageMessageStorage $manageMessageStorage
    )
    {
        $this->pickConversation = $pickConversation;
        $this->manageMessageStorage = $manageMessageStorage;
    }

    /**
     * @param string $uniqueness
     * @param string $conversation
     *
     * @return Message[]
     *
     * @throws UnauthorizedException
     */
    public function collect($uniqueness, $conversation)
    {
        $conversation = $this->pickConversation->pick($conversation);

        if (!in_array($uniqueness, $conversation->getParticipants())) {
            throw new UnauthorizedException();
        }

        /** @var \Traversable $messages */
        $messages = $this->manageMessageStorage->connect()->find([
            'conversation' => $conversation->getId()
        ]);

        return iterator_to_array($messages);
    }
}

<?php

namespace Muchacuba\Chuchuchu;

use Muchacuba\Chuchuchu\Conversation\ManageStorage as ManageConversationStorage;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class CollectConversations
{
    /**
     * @var ManageConversationStorage
     */
    private $manageConversationStorage;

    /**
     * @param ManageConversationStorage $manageConversationStorage
     */
    public function __construct(
        ManageConversationStorage $manageConversationStorage
    )
    {
        $this->manageConversationStorage = $manageConversationStorage;
    }

    /**
     * Collects conversations on which given participant belong to.
     *
     * @param string $participant
     *
     * @return Conversation[]
     */
    public function collect($participant)
    {
        /** @var \Traversable $conversations */
        $conversations = $this->manageConversationStorage->connect()->find([
            'participants' => ['$in' => [$participant]]
        ]);

        return iterator_to_array($conversations);
    }
}

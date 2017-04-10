<?php

namespace Muchacuba\Chuchuchu\Me;

use Cubalider\Unique\Uniquenesses;
use Muchacuba\Chuchuchu\Conversation\ManageStorage as ManageConversationStorage;
use Muchacuba\Chuchuchu\Conversation as RawConversation;
use Cubalider\Unique\Uniqueness\ManageStorage as ManageUniquenessStorage;
use Muchacuba\Chuchuchu\EnrichUsers;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class ResolveTouches
{
    /**
     * @var ManageConversationStorage
     */
    private $manageConversationStorage;

    /**
     * @var EnrichUsers
     */
    private $enrichUsers;

    /**
     * @var ManageUniquenessStorage
     */
    private $manageUniquenessStorage;

    /**
     * @param ManageConversationStorage $manageConversationStorage
     * @param EnrichUsers               $enrichUsers
     * @param ManageUniquenessStorage   $manageUniquenessStorage
     */
    public function __construct(
        ManageConversationStorage $manageConversationStorage,
        EnrichUsers $enrichUsers,
        ManageUniquenessStorage $manageUniquenessStorage
    )
    {
        $this->manageConversationStorage = $manageConversationStorage;
        $this->enrichUsers = $enrichUsers;
        $this->manageUniquenessStorage = $manageUniquenessStorage;
    }

    /**
     * Collects existent conversations, and adds to the array the possible
     * conversations with each of the rest of the users.
     *
     * @param string $uniqueness
     *
     * @return Touches
     */
    public function collect($uniqueness)
    {
        /** @var RawConversation[] $conversations */
        $conversations = $this->manageConversationStorage->connect()->find([
            'participants' => ['$in' => [$uniqueness]]
        ]);

        $touchConversations = [];

        // Users already in personal conversation
        $included = [];

        foreach ($conversations as $i => $conversation) {
            $receptors = array_values(array_filter(
                $conversation->getParticipants(),
                function($participant) use ($uniqueness) {
                    return $participant != $uniqueness;
                }
            ));

            $touchConversations[] = new Conversation(
                $conversation->getId(),
                $this->enrichUsers->enrich($receptors)
            );

            // Personal conversation?
            if (count($receptors) == 1) {
                $included = array_merge($included, $receptors);
            }
        }

        // Find users not in personal conversation with given user
        /** @var Uniquenesses $users */
        $users = $this->manageUniquenessStorage->connect()->find([
            '_id' => ['$nin' => array_merge([$uniqueness], $included)]
        ]);

        $touchUsers = [];
        foreach ($users as $user) {
            $touchUsers[] = $user->getId();
        }

        $touchUsers = $this->enrichUsers->enrich($touchUsers);

        return new Touches($touchConversations, $touchUsers);
    }
}

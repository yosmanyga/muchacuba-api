<?php

namespace Muchacuba\Chuchuchu;

use Muchacuba\Chuchuchu\Conversation\ManageStorage;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class ResolveConversation
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
     * @param PickConversation $pickConversation
     * @param ManageStorage    $manageStorage
     */
    public function __construct(
        PickConversation $pickConversation,
        ManageStorage $manageStorage
    )
    {
        $this->pickConversation = $pickConversation;
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param string $user
     * @param string $contact
     *
     * @return string
     */
    public function resolve($user, $contact)
    {
        try {
            return $this->pickConversation
                ->pick(null, [$user, $contact])
                ->getId();
        } catch (NonExistentConversationException $e) {
            $id = uniqid();

            $this->manageStorage->connect()->insertOne(new Conversation(
                $id,
                [$user, $contact]
            ));

            return $id;
        }
    }
}
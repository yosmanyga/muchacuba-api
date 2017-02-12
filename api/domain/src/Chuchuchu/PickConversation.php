<?php

namespace Muchacuba\Chuchuchu;

use Muchacuba\Chuchuchu\Conversation\ManageStorage;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class PickConversation
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage $manageStorage
     */
    public function __construct(ManageStorage $manageStorage)
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param string $id
     * @param array  $recipients
     *
     * @return Conversation
     *
     * @throws NonExistentConversationException
     */
    public function pick($id = null, array $recipients = null)
    {
        $criteria = [];
        if (!is_null($id)) {
            $criteria[] = ['_id' => $id];
        }

        if (!is_null($recipients)) {
            $criteria = array_merge(
                $criteria,
                array_map(function($recipient) {
                    return ['participants' => ['$in' => [$recipient]]];
                }, $recipients)
            );
        }

        /** @var Conversation $conversation */
        $conversation = $this->manageStorage->connect()
            ->findOne([
                '$and' => $criteria
            ]);

        if (is_null($conversation)) {
            throw new NonExistentConversationException();
        }

        return $conversation;
    }
}
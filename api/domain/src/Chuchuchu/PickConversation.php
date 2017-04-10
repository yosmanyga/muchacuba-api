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
     * @param array  $receptors
     *
     * @return Conversation
     *
     * @throws NonExistentConversationException
     */
    public function pick($id = null, array $receptors = null)
    {
        $criteria = [];
        if (!is_null($id)) {
            $criteria[] = ['_id' => $id];
        }

        if (!is_null($receptors)) {
            $criteria = array_merge(
                $criteria,
                array_map(function($receptor) {
                    return ['participants' => ['$in' => [$receptor]]];
                }, $receptors)
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
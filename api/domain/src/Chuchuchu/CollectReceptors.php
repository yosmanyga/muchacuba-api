<?php

namespace Muchacuba\Chuchuchu;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectReceptors
{
    /**
     * @var PickConversation
     */
    private $pickConversation;

    /**
     * @var EnrichUsers
     */
    private $enrichUsers;

    /**
     * @param PickConversation $pickConversation
     * @param EnrichUsers      $enrichUsers
     */
    public function __construct(
        PickConversation $pickConversation,
        EnrichUsers $enrichUsers
    )
    {
        $this->pickConversation = $pickConversation;
        $this->enrichUsers = $enrichUsers;
    }

    /**
     * @param string $uniqueness
     * @param string $conversation
     *
     * @return User[]
     *
     * @throws UnauthorizedException
     */
    public function collect($uniqueness, $conversation)
    {
        $conversation = $this->pickConversation->pick($conversation);

        if (!in_array($uniqueness, $conversation->getParticipants())) {
            throw new UnauthorizedException();
        }

        $receptors = [];
        foreach ($conversation->getParticipants() as $participant) {
            if ($participant != $uniqueness) {
                $receptors[] = $participant;
            }
        }

        return $this->enrichUsers->enrich($receptors);
    }
}

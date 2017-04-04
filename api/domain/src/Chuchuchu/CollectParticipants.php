<?php

namespace Muchacuba\Chuchuchu;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectParticipants
{
    /**
     * @var PickConversation
     */
    private $pickConversation;

    /**
     * @var EnrichParticipants
     */
    private $enrichParticipants;

    /**
     * @param PickConversation   $pickConversation
     * @param EnrichParticipants $enrichParticipants
     */
    public function __construct(
        PickConversation $pickConversation,
        EnrichParticipants $enrichParticipants
    )
    {
        $this->pickConversation = $pickConversation;
        $this->enrichParticipants = $enrichParticipants;
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

        return $this->enrichParticipants->enrich($conversation->getParticipants());
    }
}

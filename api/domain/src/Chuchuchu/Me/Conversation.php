<?php

namespace Muchacuba\Chuchuchu\Me;

use Muchacuba\Chuchuchu\User;

/**
 * Conversation from the user's point of view. That means, user itself is not
 * included in receptors.
 */
class Conversation implements \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var User[]
     */
    private $receptors;

    /**
     * @param string $id
     * @param User[] $receptors
     */
    public function __construct(
        $id,
        array $receptors
    ) {
        $this->id = $id;
        $this->receptors = $receptors;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User[]
     */
    public function getReceptors()
    {
        return $this->receptors;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'receptors' => $this->receptors,
        ];
    }
}

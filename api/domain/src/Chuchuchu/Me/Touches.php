<?php

namespace Muchacuba\Chuchuchu\Me;

use Muchacuba\Chuchuchu\User;

class Touches implements \JsonSerializable
{
    /**
     * @var Conversation[]
     */
    private $conversations;

    /**
     * @var User[]
     */
    private $users;

    /**
     * @param Conversation[] $conversations
     * @param User[]         $users
     */
    public function __construct(array $conversations, array $users)
    {
        $this->conversations = $conversations;
        $this->users = $users;
    }

    /**
     * @return Conversation[]
     */
    public function getConversations(): array
    {
        return $this->conversations;
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'conversations' => $this->conversations,
            'users' => $this->users,
        ];
    }
}

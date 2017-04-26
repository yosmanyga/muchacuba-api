<?php

namespace Muchacuba\Http\Chuchuchu;

use Muchacuba\Chuchuchu\Invite as DomainInvite;
use Symsonte\Http\Server;
use Muchacuba\Chuchuchu\CollectConversations as DomainCollectConversations;
use Muchacuba\Chuchuchu\CollectContacts as DomainCollectContacts;

/**
 * @di\controller({deductible: true})
 */
class Invite
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainInvite
     */
    private $invite;

    /**
     * @var DomainCollectConversations
     */
    private $collectConversations;

    /**
     * @var DomainCollectContacts
     */
    private $collectContacts;
    
    /**
     * @param Server                     $server
     * @param DomainInvite               $invite
     * @param DomainCollectConversations $collectConversations
     * @param DomainCollectContacts      $collectContacts
     */
    public function __construct(
        Server $server,
        DomainInvite $invite,
        DomainCollectConversations $collectConversations,
        DomainCollectContacts $collectContacts
    ) {
        $this->server = $server;
        $this->invite = $invite;
        $this->collectConversations = $collectConversations;
        $this->collectContacts = $collectContacts;
    }

    /**
     * @http\authorization({roles: ["chuchuchu_user"]})
     * @http\resolution({method: "POST", uri: "/chuchuchu/invite"})
     *
     * @param string $uniqueness
     */
    public function invite($uniqueness)
    {
        $post = $this->server->resolveBody();

        $this->invite->invite(
            $uniqueness,
            $post['email'],
            $post['message']
        );

        $conversations = $this->collectConversations->collect($uniqueness);
        $contacts = $this->collectContacts->collect($uniqueness);

        $this->server->sendResponse([
            'conversations' => $conversations,
            'contacts' => $contacts
        ]);
    }
}

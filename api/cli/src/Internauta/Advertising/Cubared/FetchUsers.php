<?php

namespace Muchacuba\Cli\Internauta\Advertising\Cubared;

use Symsonte\Cli\Server;
use Muchacuba\Internauta\Advertising\Cubared\FetchUsers as DomainFetchUsers;

/**
 * @di\command({deductible: true})
 */
class FetchUsers
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainFetchUsers
     */
    private $fetchUsers;

    /**
     * @param Server           $server
     * @param DomainFetchUsers $fetchUsers
     */
    public function __construct(
        Server $server,
        DomainFetchUsers $fetchUsers
    )
    {
        $this->server = $server;
        $this->fetchUsers = $fetchUsers;
    }

    /**
     * @cli\resolution({command: "internauta.advertising.cubared.fetch-users"})
     */
    public function process()
    {
        $this->fetchUsers->fetch();

//        $crawler = $client->request(
//            'POST',
//            'http://www.cubared.com/funciones_ajax.php',
//            [
//                'fn' => 'chat_enviar_mensaje_db',
//                'id_usuario_a' => 13036,
//                'mensaje' => 'Jelou!'
//            ]
//        );
    }
}

<?php

namespace Muchacuba\Internauta\Advertising\Cubared;

use Muchacuba\Internauta\Advertising\Cubared\User\ManageStorage;
use Muchacuba\Internauta\CreateClient;
use Goutte\Client;

/**
 * @di\service()
 */
class LoginMyself
{
    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $pass;

    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var CreateClient
     */
    private $createClient;

    /**
     * @param string $user
     * @param string $pass
     * @param ManageStorage $manageStorage
     * @param CreateClient $createClient
     *
     * @di\arguments({
     *     user: '%cubared_user%',
     *     pass: '%cubared_pass%'
     * })
     */
    public function __construct(
        $user,
        $pass,
        ManageStorage $manageStorage,
        CreateClient $createClient
    )
    {
        $this->user = $user;
        $this->pass = $pass;
        $this->manageStorage = $manageStorage;
        $this->createClient = $createClient;
    }

    /**
     * @return Client
     */
    function login()
    {
        $client = $this->createClient->create();

        $client->request(
            'POST',
            'http://www.cubared.com/select.php',
            [
                'acceso_usuario' => $this->user,
                'acceso_clave' => $this->pass,
                'action' => 'login',
                'recordar_clave' => 0,
                'Submit' => 'Acceder'
            ]
        );

        return $client;
    }
}
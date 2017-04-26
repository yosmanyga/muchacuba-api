<?php

namespace Muchacuba\Http\Firebase;

use Muchacuba\Firebase\UpdateProfile as DomainUpdateProfile;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class UpdateProfile
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainUpdateProfile
     */
    private $updateProfile;

    /**
     * @param Server              $server
     * @param DomainUpdateProfile $updateProfile
     */
    public function __construct(
        Server $server,
        DomainUpdateProfile $updateProfile
    ) {
        $this->server = $server;
        $this->updateProfile = $updateProfile;
    }

    /**
     * @http\authorization({roles: ["chuchuchu_user"]})
     * @http\resolution({method: "POST", uri: "/firebase/update-profile"})
     *
     * @param string $uniqueness
     */
    public function update($uniqueness)
    {
        $post = $this->server->resolveBody();

        $this->updateProfile->update(
            $uniqueness,
            $post['token']
        );

        $this->server->sendResponse();
    }
}

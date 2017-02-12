<?php

namespace Muchacuba\Http\Internauta\Server\Request\Authentication\Credential;

use Symsonte\Http\Server\Request\Authentication\Credential\Processor as BaseCredentialProcessor;
use Symsonte\Http\Server\Request\Authentication\Credential\AuthorizationResolver;

/**
 * @di\service({
 *     private: true
 * })
 */
class Processor implements BaseCredentialProcessor
{
    /**
     * @var AuthorizationResolver
     */
    private $authorizationResolver;

    /**
     * @param AuthorizationResolver $authorizationResolver
     *
     * @di\arguments({
     *     authorizationResolver: '@symsonte.http.server.request.authentication.credential.authorization_resolver'
     * })
     */
    function __construct(
        AuthorizationResolver $authorizationResolver
    )
    {
        $this->authorizationResolver = $authorizationResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {
    }
}
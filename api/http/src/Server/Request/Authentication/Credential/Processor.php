<?php

namespace Muchacuba\Http\Server\Request\Authentication\Credential;

use Symsonte\Http\Server\Request\Authentication\Credential\InvalidDataException;
use Symsonte\Http\Server\Request\Authentication\Credential\Processor as BaseCredentialProcessor;
use Symsonte\Http\Server\Request\Authentication\Credential\AuthorizationResolver;
use Symsonte\Http\Server\Request\Authentication\Credential\UnresolvableException;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;

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
     * @param AuthorizationResolver  $authorizationResolver
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
        // Resolve the credential the user sent
        try {
            $credential = $this->authorizationResolver->resolve();
        } catch (UnresolvableException $e) {
            throw $e;
        }

        $token = (new Parser())->parse($credential->getToken());
        $data = new ValidationData();
        $data->setIssuer('http://muchacuba.com');
        $data->setAudience('http://muchacuba.com');
        $data->setId('mUch@cub417');

        if ($token->validate($data) === false) {
            throw new InvalidDataException();
        }

        return $token->getClaim('uid');
    }
}

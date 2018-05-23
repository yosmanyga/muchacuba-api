<?php

namespace Muchacuba\Http;

use Symsonte\Http\Server\Request\Authentication\Credential\Processor as BaseCredentialProcessor;
use Symsonte\Http\Server\Request\Authentication\Credential\AuthorizationResolver;
use Symsonte\Http\Server\Request\Authentication\Credential\UnresolvableException;
use Firebase\Auth\Token\Exception\ExpiredToken;
use Firebase\Auth\Token\Exception\InvalidToken;
use Firebase\Auth\Token\Exception\IssuedInTheFuture;
use Firebase\Auth\Token\Verifier;
use Symsonte\Http\Server\Request\Authentication\InvalidCredentialException;
use Symsonte\Http\Server\Request\Authentication\ExpiredCredentialException;

/**
 * @di\service({
 *     private: true
 * })
 */
class CredentialProcessor implements BaseCredentialProcessor
{
    /**
     * @var AuthorizationResolver
     */
    private $authorizationResolver;

    /**
     * @var string
     */
    private $projectId;

    /**
     * @param AuthorizationResolver $authorizationResolver
     * @param string                $projectId
     *
     * @di\arguments({
     *     authorizationResolver: '@symsonte.http.server.request.authentication.credential.authorization_resolver',
     *     projectId:             '%firebase_project_id%'
     * })
     */
    function __construct(
        AuthorizationResolver $authorizationResolver,
        $projectId
    )
    {
        $this->authorizationResolver = $authorizationResolver;
        $this->projectId = $projectId;
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

//        if ($credential->getToken() === 'dev') {
//            return 'uniqueness-1';
//        }

        $verifier = new Verifier($this->projectId);

        try {
            $verifiedIdToken = $verifier->verifyIdToken($credential->getToken());

            return $verifiedIdToken->getClaim('sub');
        } catch (ExpiredToken $e) {
            throw new ExpiredCredentialException(null, null, $e);
        } catch (IssuedInTheFuture $e) {
            // Patch as a workaround when our server is fraction of second
            // behind from firebase server.
            // See https://github.com/kreait/firebase-tokens-php/issues/1
            // Just check time again
            sleep(3);
            if ($e->getToken()->getClaim('iat') > time()) {
                throw new InvalidCredentialException(null, null, $e);
            }

            return $e->getToken()->getClaim('sub');
        } catch (InvalidToken $e) {
            throw new InvalidCredentialException(null, null, $e);
        }
    }
}

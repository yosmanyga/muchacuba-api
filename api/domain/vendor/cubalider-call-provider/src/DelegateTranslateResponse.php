<?php

namespace Cubalider\Call\Provider;

use Cubalider\Call\Provider\Sinch\UnsupportedResponseException;

class DelegateTranslateResponse implements TranslateResponse
{
    /**
     * @var TranslateResponse[]
     */
    private $translateResponseServices;

    /**
     * @param TranslateResponse[] $translateResponseServices
     */
    public function __construct(array $translateResponseServices)
    {
        $this->translateResponseServices = $translateResponseServices;
    }

    /**
     * {@inheritdoc}
     */
    public function translate(Response $response, $payload = null)
    {
        foreach ($this->translateResponseServices as $translateResponseService) {
            try {
                return $translateResponseService->translate($response, $payload);
            } catch (UnsupportedResponseException $e) {
                continue;
            }
        }

        throw new UnsupportedResponseException();
    }
}
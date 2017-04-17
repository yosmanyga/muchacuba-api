<?php

namespace Cubalider\Call\Provider\Sinch;

use Cubalider\Call\Provider\DelegateTranslateResponse as BaseDelegateTranslateResponse;
use Cubalider\Call\Provider\RegisterLog;
use Cubalider\Call\Provider\Response;
use Cubalider\Call\Provider\TranslateResponse;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class DelegateTranslateResponse implements TranslateResponse
{
    /**
     * @var BaseDelegateTranslateResponse
     */
    private $delegateTranslateResponse;

    /**
     * @var RegisterLog
     */
    private $registerLog;

    /**
     * @param TranslateResponse[] $translateResponseServices
     * @param RegisterLog         $registerLog
     *
     * @di\arguments({
     *     translateResponseServices: '#cubalider.call.provider.sinch.translate_response'
     * })
     */
    public function __construct(
        array $translateResponseServices,
        RegisterLog $registerLog
    )
    {
        $this->delegateTranslateResponse = new BaseDelegateTranslateResponse($translateResponseServices);
        $this->registerLog = $registerLog;
    }

    /**
     * {@inheritdoc}
     */
    public function translate(Response $response, $payload = null)
    {
        $response = $this->delegateTranslateResponse->translate($response, $payload);

        $this->registerLog->register(
            'response-to-sinch',
            $response
        );

        return $response;
    }
}
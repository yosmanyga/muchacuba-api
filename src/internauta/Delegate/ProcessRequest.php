<?php

namespace Muchacuba\Internauta\Delegate;

use Muchacuba\Internauta\ProcessRequest as BaseProcessRequest;
use Muchacuba\Internauta\ProcessResult;
use Muchacuba\Internauta\Response;
use Muchacuba\Internauta\UnsupportedRequestException;

/**
 * @di\service()
 */
class ProcessRequest
{
    /**
     * @var BaseProcessRequest[]
     */
    private $processRequestServices;

    /**
     * @var BaseProcessRequest
     */
    private $fallbackProcessRequest;

    /**
     * @param BaseProcessRequest[] $processRequestServices
     * @param BaseProcessRequest   $fallbackProcessRequest
     *
     * @di\arguments({
     *     processRequestServices: '#internauta.process_request',
     *     fallbackProcessRequest: '@muchacuba.internauta.webmaster.process_request'
     * })
     */
    public function __construct(
        $processRequestServices,
        BaseProcessRequest $fallbackProcessRequest
    )
    {
        $this->processRequestServices = $processRequestServices;
        $this->fallbackProcessRequest = $fallbackProcessRequest;
    }

    /**
     * {@inheritdoc}
     */
    public function process($sender, $recipient, $subject, $body)
    {
        if (in_array(
            current(explode('@', $recipient)),
            ['ayuda', 'help']
        )) {
            return new ProcessResult([
                new Response(
                    'Ayuda Muchacuba <ayuda@muchacuba.com>',
                    $sender,
                    sprintf('Re: %s', $subject),
                    $this->help()
                )
            ], []);
        }

        $higher = 0;
        $service = null;
        foreach ($this->processRequestServices as $processRequest) {
            $support = $processRequest->support($sender, $recipient, $subject, $body);

            if ($support > $higher) {
                $higher = $support;
                $service = $processRequest;
            }
        }

        if ($service) {
            return $service->process($sender, $recipient, $subject, $body);
        }

        return $this->fallbackProcessRequest->process($sender, $recipient, $subject, $body);
    }

    /**
     * {@inheritdoc}
     */
    public function help()
    {
        $help = '';

        foreach ($this->processRequestServices as $processRequest) {
            $help .= sprintf("%s\n\n", $processRequest->help());
        }

        return $help;
    }
}
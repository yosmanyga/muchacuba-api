<?php

namespace Muchacuba\Internauta\Webmaster;

use Muchacuba\Internauta\ProcessRequest as BaseProcessRequest;
use Muchacuba\Internauta\ResolveSimilarity;
use Muchacuba\Internauta\Response;
use Muchacuba\Internauta\ProcessResult;
use Yosmy\Navigation\RequestPage;

/**
 * @di\service()
 */
class ProcessRequest implements BaseProcessRequest
{
    /**
     * @var ResolveSimilarity
     */
    private $resolveSimilarity;

    /**
     * @param ResolveSimilarity  $resolveSimilarity
     */
    public function __construct(
        ResolveSimilarity $resolveSimilarity
    ) {
        $this->resolveSimilarity = $resolveSimilarity;
    }

    /**
     * {@inheritdoc}
     */
    public function support($sender, $recipient, $subject, $body)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function process($sender, $recipient, $subject, $body)
    {
        return new ProcessResult(
            [
                new Response(
                    'Equipo Muchacuba <equipo@muchacuba.com>',
                    'yosmanyga@gmail.com',
                    'Fw:',
                    sprintf("Sender: %s\nRecipient: %s\nSubject: %s\nBody: %s", $sender, $recipient, $subject, $body)
                )
            ],
            []
        );
    }

    /**
     * {@inheritdoc}
     */
    public function help()
    {
        return '';
    }
}
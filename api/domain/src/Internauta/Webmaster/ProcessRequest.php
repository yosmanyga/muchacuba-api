<?php

namespace Muchacuba\Internauta\Webmaster;

use Muchacuba\Internauta\ProcessRequest as BaseProcessRequest;
use Muchacuba\Internauta\Response;
use Muchacuba\Internauta\ProcessResult;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ProcessRequest implements BaseProcessRequest
{
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
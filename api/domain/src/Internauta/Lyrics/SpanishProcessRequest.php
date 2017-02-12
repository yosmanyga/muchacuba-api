<?php

namespace Muchacuba\Internauta\Lyrics;

use Muchacuba\Internauta\UnsupportedRequestException;
use Muchacuba\Internauta\ProcessRequest as BaseProcessRequest;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{name: 'internauta.process_request', key: 'spanish_lyrics'}]
 * })
 */
class SpanishProcessRequest implements BaseProcessRequest
{
    /**
     * @var ProcessRequest
     */
    private $processRequest;

    /**
     * @param ProcessRequest $processRequest
     */
    public function __construct(ProcessRequest $processRequest)
    {
        $this->processRequest = $processRequest;
    }

    /**
     * {@inheritdoc}
     */
    public function process($sender, $recipient, $subject, $body)
    {
        if (!in_array(
            $recipient,
            [
                'letras@muchacuba.com',
                'letra@muchacuba.com'
            ]
        )) {
            throw new UnsupportedRequestException();
        }

        // Not needed
        unset($body);

        return $this->processRequest->process(
            'letra',
            $sender,
            'Letras Muchacuba <letras@muchacuba.com>',
            $subject
        );
    }

    /**
     * {@inheritdoc}
     */
    public function help()
    {
        return <<<EOF
Escribe a letras@muchacuba.com para recibir letras de canciones.
En el asunto escribe el artista, título o parte de la letra.
EOF;
    }
}

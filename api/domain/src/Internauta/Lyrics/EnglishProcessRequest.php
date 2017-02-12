<?php

namespace Muchacuba\Internauta\Lyrics;

use Muchacuba\Internauta\UnsupportedRequestException;
use Muchacuba\Internauta\ProcessRequest as BaseProcessRequest;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{name: 'internauta.process_request', key: 'english_lyrics'}]
 * })
 */
class EnglishProcessRequest implements BaseProcessRequest
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
                'lyrics@muchacuba.com',
                'lyric@muchacuba.com',
                'letter@muchacuba.com'
            ]
        )) {
            throw new UnsupportedRequestException();
        }

        // Not needed
        unset($body);

        return $this->processRequest->process(
            'lyrics',
            $sender,
            'Lyrics Muchacuba <lyrics@muchacuba.com>',
            $subject
        );
    }

    /**
     * {@inheritdoc}
     */
    public function help()
    {
        return <<<EOF
Escribe a lyrics@muchacuba.com para recibir letras de canciones en inglés.
En el asunto escribe el artista, título o parte de la letra.
EOF;
    }
}

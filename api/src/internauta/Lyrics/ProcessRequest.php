<?php

namespace Muchacuba\Internauta\Lyrics;

use Muchacuba\Internauta\UnsupportedRequestException;
use Muchacuba\Internauta\ProcessRequest as BaseProcessRequest;
use Muchacuba\Internauta\Event;
use Muchacuba\Internauta\Response;
use Muchacuba\Internauta\ProcessResult;
use Muchacuba\Internauta\SearchGoogle;

/**
 * @di\service({
 *     tags: [{name: 'internauta.process_request', key: 'lyrics'}]
 * })
 */
class ProcessRequest implements BaseProcessRequest
{
    /**
     * @var string
     */
    private $googleServerApi;

    /**
     * @var string
     */
    private $googleCx;

    /**
     * @var SearchGoogle
     */
    private $searchGoogle;

    /**
     * @var DelegateReadLyrics
     */
    private $delegateReadLyrics;

    /**
     * @param string             $googleServerApi
     * @param string             $googleCx
     * @param SearchGoogle       $searchGoogle
     * @param DelegateReadLyrics $delegateReadLyrics
     *
     * @di\arguments({
     *     googleServerApi: '%google_server_api%',
     *     googleCx:        '%google_cx_lyrics%'
     * })
     */
    public function __construct(
        $googleServerApi,
        $googleCx,
        SearchGoogle $searchGoogle,
        DelegateReadLyrics $delegateReadLyrics
    )
    {
        $this->googleServerApi = $googleServerApi;
        $this->googleCx = $googleCx;
        $this->searchGoogle = $searchGoogle;
        $this->delegateReadLyrics = $delegateReadLyrics;
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
                'letra@muchacuba.com',
                'lyrics@muchacuba.com',
                'lyric@muchacuba.com',
                'letter@muchacuba.com'
            ]
        )) {
            throw new UnsupportedRequestException();
        }

        $responses = [];
        $events = [];

        if (!$subject) {
            $responses[] = new Response(
                "Letras Muchacuba <letras@muchacuba.com>",
                $sender,
                sprintf('Re: %s', $subject),
                'Debes escribir lo que buscas en el asunto del correo.'
            );

            return new ProcessResult($responses, $events);
        }

        $results = $this->searchGoogle->search(
            $this->googleServerApi,
            $this->googleCx,
            $subject
        );

        foreach ($results as $result) {
            try {
                list($author, $title, $lyrics) = $this->delegateReadLyrics->read($result['link']);

                $str = $author . $title;

                $events[] = new Event(
                    $this,
                    'Found',
                    [
                        'link' => $result['link']
                    ]
                );

                if (
                    strpos($str, 'Lyrics') !== false
                    || strpos($str, 'Letra') !== false
                ) {
                    $events[] = new Event(
                        $this,
                        'QuestionableParsing',
                        [
                            'link' => $result['link']
                        ]
                    );
                }

                break;
            } catch (UnsupportedLinkException $e) {
                $events[] = new Event(
                    $this,
                    'UnsupportedLink',
                    [
                        'link' => $result['link']
                    ]
                );

                continue;
            } catch (\Exception $e) {
                $events[] = new Event(
                    $this,
                    'Exception',
                    [
                        'link' => $result['link'],
                        'exception' => $e->__toString()
                    ]
                );

                continue;
            }
        }

        // Found and read lyrics?
        if (isset($author, $title, $lyrics)) {
            $body = sprintf("%s\n%s\n\n%s", $author, $title, $lyrics);

            if (strlen($body) > 5000) {
                $events[] = new Event(
                    $this,
                    'BigBody',
                    [
                        'body' => $body
                    ]
                );

                return new ProcessResult([], $events);
            }

            if (strpos($body, '<') !== false) {
                $events[] = new Event(
                    $this,
                    'HtmlIncluded',
                    []
                );

                return new ProcessResult([], $events);
            }
        } else {
            // Google didn't find lyrics?
            if (empty($results)) {
                $body = 'Lo sentimos, no pudimos encontrar la letra de esa canción. Intenta usar otras palabras.';

                $events[] = new Event(
                    $this,
                    'NotFound',
                    []
                );
            }
            // Google found it but there was not reader
            else {
                $events[] = new Event(
                    $this,
                    'UnsupportedLinks',
                    []
                );

                return new ProcessResult([], $events);
            }
        }

        $responses[] = new Response(
            "Letras Muchacuba <letras@muchacuba.com>",
            $sender,
            sprintf('Re: %s', $subject),
            $body
        );

        return new ProcessResult($responses, $events);
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

<?php

namespace Muchacuba\Internauta\Lyrics;

use Muchacuba\Internauta\ResolveSimilarity;
use Muchacuba\Internauta\ProcessRequest as BaseProcessRequest;
use Muchacuba\Internauta\Event;
use Muchacuba\Internauta\Response;
use Muchacuba\Internauta\ProcessResult;
use Muchacuba\Internauta\SearchGoogle;
use Exception;

/**
 * @di\service({
 *     tags: [{name: 'internauta.process_request', key: 'lyrics'}]
 * })
 */
class ProcessRequest implements BaseProcessRequest
{
    /**
     * @var ResolveSimilarity
     */
    private $resolveSimilarity;

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
     * @param ResolveSimilarity  $resolveSimilarity
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
        ResolveSimilarity $resolveSimilarity,
        $googleServerApi,
        $googleCx,
        SearchGoogle $searchGoogle,
        DelegateReadLyrics $delegateReadLyrics
    ) {
        $this->resolveSimilarity = $resolveSimilarity;
        $this->googleServerApi = $googleServerApi;
        $this->googleCx = $googleCx;
        $this->searchGoogle = $searchGoogle;
        $this->delegateReadLyrics = $delegateReadLyrics;
    }

    /**
     * {@inheritdoc}
     */
    public function support($sender, $recipient, $subject, $body)
    {
        return $this->resolveSimilarity->resolve(
            ['letras', 'lyrics', 'letter'],
            $recipient
        );
    }

    /**
     * {@inheritdoc}
     */
    public function process($sender, $recipient, $subject, $body)
    {
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
            } catch (Exception $e) {
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

            // i.e.: https://www.songstraducidas.com/letratraducida-Mirrors_48347.htm
            // is 8595 length
            if (strlen($body) > 10000) {
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
A veces se puede recibir la traducción de la letra si se agrega: (traducir a español)
EOF;
    }
}

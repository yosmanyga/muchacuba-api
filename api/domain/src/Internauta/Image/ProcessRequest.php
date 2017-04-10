<?php

namespace Muchacuba\Internauta\Image;

use Muchacuba\Internauta\Event;
use Muchacuba\Internauta\ProcessRequest as BaseProcessRequest;
use Muchacuba\Internauta\Response;
use Muchacuba\Internauta\ProcessResult;
use Muchacuba\Internauta\SearchGoogle;
use Muchacuba\Internauta\UnsupportedRequestException;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{name: 'internauta.process_request', key: 'image'}]
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
     * @param string       $googleServerApi
     * @param string       $googleCx
     * @param SearchGoogle $searchGoogle
     *
     * @di\arguments({
     *     googleServerApi: '%google_server_api%',
     *     googleCx:        '%google_cx_images%'
     * })
     */
    public function __construct(
        $googleServerApi,
        $googleCx,
        SearchGoogle $searchGoogle
    )
    {
        $this->googleServerApi = $googleServerApi;
        $this->googleCx = $googleCx;
        $this->searchGoogle = $searchGoogle;
    }

    /**
     * {@inheritdoc}
     */
    public function process($sender, $receptor, $subject, $body)
    {
        if (!in_array(
            current(explode('@', $receptor)),
            ['imagenes', 'imagen', 'images', 'image']
        )) {
            throw new UnsupportedRequestException();
        }

        // Not needed
        unset($body);

        $responses = [];
        $events = [];

        $amount = $this->resolveAmount($subject, 3, 20);

        $subject = $this->cleanSubject($subject);

        $c = 0;
        $start = 1;
        while ($c < $amount) {
            $results = $this->searchGoogle->search(
                $this->googleServerApi,
                $this->googleCx,
                $subject,
                $amount,
                $start,
                'searchType=image&imgSize=large'
            );

            if (empty($results)) {
                break;
            }

            // Results could be lower than requested amount
            $amount = min($amount, count($results));

            foreach ($results as $result) {
                try {
                    $image = base64_encode(file_get_contents(
                        $result['link'],
                        false,
                        stream_context_create(array(
                            'http' => array(
                                'timeout' => 5
                            )
                        ))
                    ));
                }
                // Problem downloading the image?
                catch (\Exception $e) {
                    $events[] = new Event(
                        $this,
                        'Invalid',
                        [
                            'link' => $result['link']
                        ]
                    );

                    continue;
                }

                $size = strlen($image);
                if ($size > 1000000) {
                    $events[] = new Event(
                        $this,
                        'Heavy',
                        [
                            'link' => $result['link']
                        ]
                    );

                    continue;
                }

                $responses[] = new Response(
                    'Imágenes Muchacuba <imagenes@muchacuba.com>',
                    $sender,
                    sprintf('Re: %s [%s de %s]', $subject, ++$c, $amount),
                    'En los adjuntos está la imagen encontrada.',
                    [$image]
                );

                if ($c == $amount) {
                    break 2;
                }
            }

            $start += $amount;
        }

        return new ProcessResult($responses, $events);
    }

    /**
     * {@inheritdoc}
     */
    public function help()
    {
        return <<<EOF
Escribe a imagenes@muchacuba.com para recibir imágenes desde internet.
En el asunto escribe las palabras claves para buscar las imágenes, ej: josé martí
Para recibir más de 3 imágenes escribe el número entre corchetes, ej: josé martí [5]
EOF;
    }

    /**
     * @param string $subject
     * @param int    $default
     * @param int    $max
     *
     * @return int
     */
    private function resolveAmount($subject, $default, $max)
    {
        if (preg_match("/\[([0-9]+)\]/", $subject, $match) === 1) {
            return min((int) $match[1], $max);
        }

        return $default;
    }

    /**
     * @param string $subject
     *
     * @return string
     */
    private function cleanSubject($subject)
    {
        $subject = preg_replace("/\[[0-9]\]/", '', $subject);
        $subject = trim($subject);

        return $subject;
    }
}
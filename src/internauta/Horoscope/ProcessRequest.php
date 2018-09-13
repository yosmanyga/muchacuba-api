<?php

namespace Muchacuba\Internauta\Horoscope;

use Yosmy\Navigation\RequestPage;
use Muchacuba\Internauta\Event;
use Muchacuba\Internauta\ProcessRequest as BaseProcessRequest;
use Muchacuba\Internauta\Response;
use Muchacuba\Internauta\ProcessResult;
use Muchacuba\Internauta\SearchGoogle;
use Muchacuba\Internauta\UnsupportedRequestException;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     tags: [{name: 'internauta.process_request', key: 'horoscope'}]
 * })
 */
class ProcessRequest implements BaseProcessRequest
{
    /**
     * {@inheritdoc}
     */
    public function process($sender, $recipient, $subject, $body)
    {
        if (!in_array(
            current(explode('@', $recipient)),
            ['horoscopo', 'horozcopo', 'hooroscopo', 'oroscopo', 'zodiaco', 'sodiaco']
        )) {
            throw new UnsupportedRequestException();
        }

        $responses = [];
        $events = [];

        $body = $this->readHoroscope($subject);

        $responses[] = new Response(
            'Horóscopo Muchacuba <horoscopo@muchacuba.com>',
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
Escribe a horoscopo@muchacuba.com para recibir el horóscopo del día.
En el asunto escribe tu signo del zodiaco.
EOF;
    }

    /**
     * @param string $subject
     *
     * @return string
     */
    private function readHoroscope($subject)
    {
        $data = file_get_contents('https://api.adderou.cl/tyaas/');

        $data = json_decode($data, true);

        $shortest = -1;
        foreach ($data['horoscopo'] as $sign => $rows) {
            $lev = levenshtein($subject, $rows['nombre']);

            // Exact match?
            if ($lev == 0) {
                $closest = $rows;
                $shortest = 0;

                break;
            }

            // If this distance is less than the next found shortest
            // distance, OR if a next shortest word has not yet been found
            if ($lev <= $shortest || $shortest < 0) {
                // Set the closest match, and shortest distance
                $closest = $rows;
                $shortest = $lev;
            }
        }

        $body = sprintf(
            "Signo: %s\n\nFecha: %s\n\nAmor: %s\n\nSalud: %s\n\nDinero: %s\n\nColor: %s\n\nNúmero: %s\n\n\n\n",
            $closest['nombre'],
            $closest['fechaSigno'],
            $closest['amor'],
            $closest['salud'],
            $closest['dinero'],
            $closest['color'],
            $closest['numero']
        );

        return $body;
    }
}

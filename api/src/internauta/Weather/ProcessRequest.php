<?php

namespace Muchacuba\Internauta\Weather;

use Cubalider\Navigation\RequestPage;
use Muchacuba\Internauta\Event;
use Muchacuba\Internauta\Response;
use Muchacuba\Internauta\ProcessResult;
use Muchacuba\Internauta\SearchGoogle;
use Muchacuba\Internauta\ProcessRequest as BaseProcessRequest;
use Muchacuba\Internauta\UnsupportedRequestException;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     tags: [{name: 'internauta.process_request', key: 'weather'}]
 * })
 */
class ProcessRequest implements BaseProcessRequest
{
    /**
     * @var RequestPage
     */
    private $requestPage;

    /**
     * @param RequestPage  $requestPage
     */
    public function __construct(
        RequestPage $requestPage
    )
    {
        $this->requestPage = $requestPage;
    }

    /**
     * {@inheritdoc}
     */
    public function process($sender, $recipient, $subject, $body)
    {
        if (!in_array(
            current(explode('@', $recipient)),
            ['tiempo', 'pronostico', 'weather', 'meteorologia', 'ciclon', 'huracan', 'estado']
        )) {
            throw new UnsupportedRequestException();
        }

        // Not needed
        unset($body);

        $responses = [];
        $events = [];

        $body = $this->getWeather();

        $responses[] = new Response(
            'Muchacuba <tiempo@muchacuba.com>',
            $sender,
            'Estado del tiempo',
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
Escribe a tiempo@muchacuba.com para recibir el estado del tiempo.
EOF;
    }

    /**
     * @return string
     */
    private function getWeather()
    {
        $crawler = $this->requestPage->request('http://www.met.inf.cu/asp/genesis.asp?TB0=PLANTILLAS&TB1=PT&TB2=/Pronostico/pttn.txt');

        $header = $crawler
            ->filter('.tablaborde .contenidoPagina b')
            ->text();

        $body = $crawler
            ->filter('.tablaborde .contenidoPagina p')
            ->html();
        $body = preg_replace(
            [
                '/<font(.*?)>(.*?)<\/font>/s',
                '/<br(.*?)>/s',
            ],
            '',
            $body
        );

        return sprintf("%s\n\n%s", $header, $body);
    }
}
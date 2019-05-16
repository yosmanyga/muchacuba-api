<?php

namespace Muchacuba\Internauta\Weather;

use Muchacuba\Internauta\ResolveSimilarity;
use Yosmy\Navigation\RequestPage;
use Muchacuba\Internauta\Response;
use Muchacuba\Internauta\ProcessResult;
use Muchacuba\Internauta\ProcessRequest as BaseProcessRequest;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     tags: [{name: 'internauta.process_request', key: 'weather'}]
 * })
 */
class ProcessRequest implements BaseProcessRequest
{
    /**
     * @var ResolveSimilarity
     */
    private $resolveSimilarity;

    /**
     * @var RequestPage
     */
    private $requestPage;

    /**
     * @param ResolveSimilarity  $resolveSimilarity
     * @param RequestPage        $requestPage
     */
    public function __construct(
        ResolveSimilarity $resolveSimilarity,
        RequestPage $requestPage
    ) {
        $this->resolveSimilarity = $resolveSimilarity;
        $this->requestPage = $requestPage;
    }

    /**
     * {@inheritdoc}
     */
    public function support($sender, $recipient, $subject, $body)
    {
        return $this->resolveSimilarity->resolve(
            ['tiempo', 'pronostico', 'weather', 'meteorologia', 'ciclon', 'huracan', 'estado'],
            $recipient
        );
    }

    /**
     * {@inheritdoc}
     */
    public function process($sender, $recipient, $subject, $body)
    {
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
        $crawler = $this->requestPage->request('http://tiempo.cuba.cu/');

        $body = $crawler
            ->filter('#contt3 > div > div > div')
            ->each(function(Crawler $crawler) {
                return trim($crawler->text());
            });
        $body = implode("\n", $body);

        return $body;
    }

// It doesn't work. Maybe the problem is related to google app engine?
//
//    /**
//     * @return string
//     */
//    private function getWeatherForToday()
//    {
//        $crawler = $this->requestPage->request('http://www.met.inf.cu/asp/genesis.asp?TB0=PLANTILLAS&TB1=PT&TB2=/Pronostico/pttn.txt');
//
//        $header = $crawler
//            ->filter('.tablaborde .contenidoPagina b')
//            ->text();
//
//        $body = $crawler
//            ->filter('.tablaborde .contenidoPagina p')
//            ->html();
//        $body = preg_replace(
//            [
//                '/<font(.*?)>(.*?)<\/font>/s',
//                '/<br(.*?)>/s',
//            ],
//            '',
//            $body
//        );
//
//        return sprintf("%s\n\n%s", $header, $body);
//    }
//
//    /**
//     * @return string
//     */
//    private function getWeatherForTomorrow()
//    {
//        $crawler = $this->requestPage->request('http://www.met.inf.cu/asp/genesis.asp?TB0=PLANTILLAS&TB1=PTM&TB2=/Pronostico/Ptm.txt');
//
//        $header = $crawler
//            ->filter('.bordeBlanco .contenidoPagina b')
//            ->text();
//
//        $body = $crawler
//            ->filter('.bordeBlanco .contenidoPagina p')
//            ->html();
//        $body = preg_replace(
//            [
//                '/<font(.*?)>(.*?)<\/font>/s',
//                '/<br(.*?)>/s',
//            ],
//            '',
//            $body
//        );
//
//        return sprintf("%s\n\n%s", $header, $body);
//    }
}
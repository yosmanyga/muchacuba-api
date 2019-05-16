<?php

namespace Muchacuba\Internauta\Horoscope;

use Muchacuba\Internauta\ResolveSimilarity;
use Yosmy\Navigation\RequestPage;
use Muchacuba\Internauta\Event;
use Muchacuba\Internauta\ProcessRequest as BaseProcessRequest;
use Muchacuba\Internauta\Response;
use Muchacuba\Internauta\ProcessResult;
use Muchacuba\Internauta\SearchGoogle;
use Symfony\Component\DomCrawler\Crawler;
use IntlDateFormatter;

/**
 * @di\service({
 *     tags: [{name: 'internauta.process_request', key: 'horoscope'}]
 * })
 */
class UnivisionProcessRequest implements BaseProcessRequest
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
     * @var RequestPage
     */
    private $requestPage;

    /**
     * @param ResolveSimilarity $resolveSimilarity
     * @param string            $googleServerApi
     * @param string            $googleCx
     * @param SearchGoogle      $searchGoogle
     * @param RequestPage       $requestPage
     *
     * @di\arguments({
     *     googleServerApi: '%google_server_api%',
     *     googleCx:        '%google_cx_horoscope%'
     * })
     */
    public function __construct(
        ResolveSimilarity $resolveSimilarity,
        $googleServerApi,
        $googleCx,
        SearchGoogle $searchGoogle,
        RequestPage $requestPage
    ) {
        $this->resolveSimilarity = $resolveSimilarity;
        $this->googleServerApi = $googleServerApi;
        $this->googleCx = $googleCx;
        $this->searchGoogle = $searchGoogle;
        $this->requestPage = $requestPage;
    }

    /**
     * {@inheritdoc}
     */
    public function support($sender, $recipient, $subject, $body)
    {
        return $this->resolveSimilarity->resolve(
            ['horoscopo', 'zodiaco'],
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

        $results = $this->searchGoogle->search(
            $this->googleServerApi,
            $this->googleCx,
            sprintf(
                'horoscopo %s %s',
                // Used this parameter to find the exact horoscope page for current day
                (new IntlDateFormatter(
                    'es',
                    IntlDateFormatter::FULL,
                    IntlDateFormatter::FULL,
                    'America/Havana',
                    IntlDateFormatter::GREGORIAN,
                    "eeee d 'de' LLLL Y"
                ))->format(time()),
                $subject
            )
        );

        if (count($results) == 0) {
            $events[] = new Event(
                $this,
                'NotFound',
                [
                    'subject' => $subject
                ]
            );

            return new ProcessResult($responses, $events);
        }

        $body = null;

        foreach ($results as $result) {
            $body = $this->readHoroscope1($result['link']);

            break;
        }

        if ($body === null) {
            $events[] = new Event(
                $this,
                'NotFound',
                []
            );

            return new ProcessResult($responses, $events);
        }

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
     * @param string $link
     *
     * @return string
     */
    private function readHoroscope1($link)
    {
        $crawler = $this->requestPage->request($link, false);

        $title = $crawler
            ->filter('h1')
            ->first()->getNode(0)->textContent;

        $body = sprintf("%s", $title);

        $texts = $crawler
            ->filter('meta[itemProp="caption"]')
            ->each(function(Crawler $crawler) {
                $text = $crawler->first()->getNode(0)->getAttribute('content');
                $text = str_replace(['<b>', '</b>'], '', $text);
                $text = str_replace(['<i>', '</i>'], '', $text);
                $text = str_replace(['<br/>', ' -'], "\n", $text);
                $text = str_replace(['&quot;'], '', $text);

                return $text;
            });

        $texts = array_filter(
            $texts,
            function($text) {
                return !empty($text);
            }
        );

        if (!$texts) {
            return $this->readHoroscope2($link);
        }

        $text = implode("\n\n", $texts);

        $body = sprintf("%s\n\n%s", $body, $text);

        return $body;
    }

    /**
     * @param string $link
     *
     * @return string
     */
    private function readHoroscope2($link)
    {
        $crawler = $this->requestPage->request($link, false);

        $title = $crawler
            ->filter('h1')
            ->first()->getNode(0)->textContent;

        $body = sprintf("%s", $title);

        $texts = $crawler
            ->filter('#article-chunks p, #article-chunks h3')
            ->each(function(Crawler $crawler) {
                if ($crawler->first()->getNode(0)->tagName == 'h3') {
                    $text = sprintf("%s\n", $crawler->first()->getNode(0)->textContent);
                    $text = trim($text);
                } else {
                    $text = implode("\n", $crawler->filterXPath('//p/text()')->extract(['_text']));
                    $text = trim($text);
                }

                return $text;
            });

        $texts = array_filter(
            $texts,
            function($text) {
                return !empty($text);
            }
        );

        $text = implode("\n\n", $texts);

        $body = sprintf("%s\n\n%s", $body, $text);

        return $body;
    }
}

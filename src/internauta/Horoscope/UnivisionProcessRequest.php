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
 * @di\service()
 */
class UnivisionProcessRequest implements BaseProcessRequest
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
     * @var RequestPage
     */
    private $requestPage;

    /**
     * @param string       $googleServerApi
     * @param string       $googleCx
     * @param SearchGoogle $searchGoogle
     * @param RequestPage  $requestPage
     *
     * @di\arguments({
     *     googleServerApi: '%google_server_api%',
     *     googleCx:        '%google_cx_horoscope%'
     * })
     */
    public function __construct(
        $googleServerApi,
        $googleCx,
        SearchGoogle $searchGoogle,
        RequestPage $requestPage
    )
    {
        $this->googleServerApi = $googleServerApi;
        $this->googleCx = $googleCx;
        $this->searchGoogle = $searchGoogle;
        $this->requestPage = $requestPage;
    }

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
                (new \IntlDateFormatter(
                    'es',
                    \IntlDateFormatter::FULL,
                    \IntlDateFormatter::FULL,
                    'America/Havana',
                    \IntlDateFormatter::GREGORIAN,
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
            $body = $this->readHoroscope($result['link']);

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
    private function readHoroscope($link)
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

<?php

namespace Muchacuba\Internauta\Horoscope;

use Muchacuba\Internauta\CreateClient;
use Muchacuba\Internauta\Event;
use Muchacuba\Internauta\ProcessRequest as BaseProcessRequest;
use Muchacuba\Internauta\Response;
use Muchacuba\Internauta\ProcessResult;
use Muchacuba\Internauta\SearchGoogle;
use Muchacuba\Internauta\UnsupportedRequestException;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{name: 'internauta.process_request', key: 'horoscope'}]
 * })
 */
class ProcessRequest implements BaseProcessRequest
{
    /**
     * @var string
     */
    private $googleKey;

    /**
     * @var string
     */
    private $googleCx;

    /**
     * @var SearchGoogle
     */
    private $searchGoogle;

    /**
     * @var CreateClient
     */
    private $createClient;

    /**
     * @param string       $googleKey
     * @param string       $googleCx
     * @param SearchGoogle $searchGoogle
     * @param CreateClient $createClient
     *
     * @di\arguments({
     *     googleKey: '%google_key%',
     *     googleCx:  '%google_cx_horoscope%'
     * })
     */
    public function __construct(
        $googleKey,
        $googleCx,
        SearchGoogle $searchGoogle,
        CreateClient $createClient
    )
    {
        $this->googleKey = $googleKey;
        $this->googleCx = $googleCx;
        $this->searchGoogle = $searchGoogle;
        $this->createClient = $createClient;
    }

    /**
     * {@inheritdoc}
     */
    public function process($sender, $recipient, $subject, $body)
    {
        if (!in_array(
            current(explode('@', $recipient)),
            ['horoscopo', 'oroscopo', 'zodiaco', 'sodiaco']
        )) {
            throw new UnsupportedRequestException();
        }

        // Not needed
        unset($body);

        $responses = [];
        $events = [];

        $results = $this->searchGoogle->search(
            $this->googleKey,
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

        $requests[] = new Response(
            'Horóscopo Muchacuba <horoscopo@muchacuba.com>',
            $sender,
            sprintf('Re: %s', $subject),
            $body
        );

        return new ProcessResult($requests, $events);
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
        $crawler = $this->createClient->create()
            ->request('GET', $link);

        $title = $crawler
            ->filter('.uvn-flex-article h1')
            ->first()->getNode(0)->textContent;
        // The title comes with spaces at the beginning and end
        $title = trim($title);

        $text = $crawler
            ->filter('.uvn-flex-article-body p')
            ->each(function(Crawler $crawler) {
                $titleCrawler = $crawler->filter('b');

                if ($titleCrawler->count() != 0) {
                    $title = sprintf("%s\n", $titleCrawler->first()->getNode(0)->textContent);
                } else {
                    $title = '';
                }

                $text = implode("\n", $crawler->filterXPath('//p/text()')->extract(['_text']));
                $text = trim($text);

                return sprintf("%s%s", $title, $text);
            });
        $text = implode("\n\n", $text);

        return sprintf("%s\n\n%s", $title, $text);
    }
}
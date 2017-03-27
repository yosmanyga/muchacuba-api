<?php

namespace Muchacuba\Internauta\Revolico;

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
 *     deductible: true,
 *     tags: [{name: 'internauta.process_request', key: 'revolico'}]
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
     * @var RequestPage
     */
    private $requestPage;

    /**
     * @param string       $googleKey
     * @param string       $googleCx
     * @param SearchGoogle $searchGoogle
     * @param RequestPage $requestPage
     *
     * @di\arguments({
     *     googleKey: '%google_key%',
     *     googleCx:  '%google_cx_revolico%'
     * })
     */
    public function __construct(
        $googleKey,
        $googleCx,
        SearchGoogle $searchGoogle,
        RequestPage $requestPage
    )
    {
        $this->googleKey = $googleKey;
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
            ['revolico', 'rebolico', 'anuncios', 'anuncio']
        )) {
            throw new UnsupportedRequestException();
        }

        // Not needed
        unset($body);

        $responses = [];
        $events = [];

        $amount = $this->resolveAmount($subject, 3);
        $withImages = $this->resolveWithImages($subject);

        $subject = $this->cleanSubject($subject);

        $c = 0;
        $start = 1;
        while ($c < $amount) {
            $results = $this->searchGoogle->search(
                $this->googleKey,
                $this->googleCx,
                $subject,
                $amount,
                $start
            );

            if (empty($results)) {
                break;
            }

            foreach ($results as $result) {
                $crawler = $this->requestPage->request($result['link']);

                // Expired ad?
                if ($crawler->filter('.errorText')->count() != 0) {
                    continue;
                }

                // List page?
                if (
                    $crawler->filter('.headingText1')->count() != 0
                    && $crawler->filter('.headingText1')->first()->text() == 'Insertar un anuncio'
                ) {
                    continue;
                }

                $title = $this->pickTitle($crawler);

                $personal = $this->pickPersonal($crawler);

                $text = $this->pickText($crawler);

                if ($withImages === true) {
                    $images = $this->pickImages($crawler);

                    // TODO: Throw exception if there are more than 3 images
                } else {
                    $images = [];
                }

                $responses[] = new Response(
                    'Revolico Muchacuba <revolico@muchacuba.com>',
                    $sender,
                    sprintf('Re: %s [%s de %s]', $subject, ++$c, $amount),
                    sprintf(
                        "%s\n\n%s\n\n%s\n%s",
                        $title,
                        $text,
                        implode("\n", $personal),
                        sprintf("Enlace: %s", $crawler->getUri())
                    ),
                    $images
                );

                if ($c == $amount) {
                    break 2;
                }
            }

            $start += $amount;
        }

        if (empty($responses)) {
            $events[] = new Event(
                $this,
                'NotFound',
                []
            );

            $responses[] = new Response(
                'Revolico Muchacuba <revolico@muchacuba.com>',
                $sender,
                sprintf('Re: %s', $subject),
                "Lo sentimos, no encontramos ningún anuncio. Intenta usar otras palabras."
            );
        }

        return new ProcessResult($responses, $events);
    }

    /**
     * {@inheritdoc}
     */
    public function help()
    {
        return <<<EOF
Escribe a revolico@muchacuba.com para recibir anuncios de revolico.
En el asunto escribe las palabras a buscar, ej: laptop core i7
Para recibir más de 3 anuncios escribe el número entre corchetes, ej: laptop core i7 [10]
Para recibir los anuncios con sus fotos agrega la letra f, ej: laptop core i7 [10f]
EOF;
    }

    /**
     * @param string $subject
     * @param int    $default
     *
     * @return int
     */
    private function resolveAmount($subject, $default)
    {
        if (
            preg_match("/\[(\d+)?(f)?\]/", $subject, $match) === 1
            && $match[1] !== ''
        ) {
            return (int) $match[1];
        }

        return $default;
    }

    /**
     * @param string $subject
     *
     * @return bool
     */
    private function resolveWithImages($subject)
    {
        return
            preg_match("/\[(\d+)?(f)?\]/", $subject, $match) === 1
            && isset($match[2]);
    }

    /**
     * @param string $subject
     *
     * @return string
     */
    private function cleanSubject($subject)
    {
        $subject = preg_replace("/\[(\d+)?(f)?\]/", '', $subject);
        $subject = trim($subject);

        return $subject;
    }
    
    /**
     * @param Crawler $crawler
     *
     * @return string
     */
    private function pickTitle(Crawler $crawler)
    {
        $crawler = $crawler->filter('#adwrap h1');

        return $crawler->first()->getNode(0)->textContent;
    }

    /**
     * @param Crawler $crawler
     *
     * @return array
     */
    private function pickPersonal(Crawler $crawler)
    {
        $crawler = $crawler->filter('#adwrap #lineBlock');

        return $crawler->each(function(Crawler $crawler) {
            $keyCrawler = $crawler->filter('.headingText2');

            $key = $keyCrawler->first()->getNode(0)->textContent;

            $valueCrawler = $crawler->filter('.normalText');

            $value = trim($valueCrawler->first()->getNode(0)->textContent);

            if ($key == 'Email: ') {
                $keyCrawler = $crawler->filter('.normalText .__cf_email__');

                $attr = $keyCrawler->attr('data-cfemail');

                $value = $this->decodeRequest($attr);
            }

            return sprintf(
                "%s %s",
                $key,
                $value
            );
        });
    }

    /**
     * @param Crawler $crawler
     *
     * @return string[]
     */
    private function pickImages(Crawler $crawler)
    {
        return $crawler->filter('#adwrap table.view img')->each(function (Crawler $crawler) {
            return base64_encode(
                file_get_contents(
                    sprintf("http://revolico.com%s", $crawler->attr('src'))
                )
            );
        });
    }

    /**
     * @param Crawler $crawler
     *
     * @return string
     */
    private function pickText(Crawler $crawler)
    {
        $crawler = $crawler->filter('#textwrap .showAdText');

        $html = $crawler->html();

        $crawler->children()->each(function(Crawler $crawler) use (&$html) {
            $name = $crawler->first()->nodeName();

            if ($name == 'br') {
                $html = str_replace('<br>', '', $html);
            } elseif ($name == 'a') {
                $html = preg_replace(
                    '/<a(.*?)>(.*?)<\/a>/',
                    $this->decodeRequest($crawler->attr('data-cfrequest')),
                    $html,
                    1
                );
            } elseif ($name == 'script') {
                $html = preg_replace(
                    '/<script(.*?)>(.*?)<\/script>/',
                    '',
                    $html,
                    1
                );
            }
        });

        return $html;
    }

    /**
     * @author http://blog.safebuff.com/2016/06/01/Cloudflare-Request-Protection-Decoder/
     *
     * @param string $c
     *
     * @return string
     */
    private function decodeRequest($c)
    {
        $k = hexdec(substr($c, 0, 2));

        for($i = 2, $m = ''; $i < strlen($c) - 1; $i += 2) {
            $m .= chr(hexdec(substr($c, $i, 2)) ^ $k);
        }

        return $m;
    }
}
<?php

namespace Muchacuba\Internauta\Lyrics;

use Yosmy\Navigation\RequestPage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     tags: [{name: 'internauta.lyrics.read_lyrics', key: 'letras'}]
 * })
 */
class LetrasReadLyrics implements ReadLyrics
{
    /**
     * @var RequestPage
     */
    private $requestPage;

    /**
     * @param RequestPage $requestPage
     */
    public function __construct(RequestPage $requestPage)
    {
        $this->requestPage = $requestPage;
    }

    /**
     * {@inheritdoc}
     */
    public function read($link)
    {
        if (
            parse_url($link, PHP_URL_HOST) !== 'www.letras.com'
            // Top lyrics pages
            || strpos($link, 'mais-acessadas') !== false
        ) {
            throw new UnsupportedLinkException();
        }

        $crawler = $this->requestPage->request($link);

        $author = $this->resolveAuthor($crawler);

        $title = $this->resolveTitle($crawler);

        $lyrics = $this->resolveLyrics($crawler);

        return [$author, $title, $lyrics];
    }

    /**
     * @param Crawler $crawler
     *
     * @return string
     *
     * @throws UnsupportedLinkException
     */
    private function resolveAuthor(Crawler $crawler)
    {
        $crawler = $crawler->filter('#js-lyric-cnt h2');

        if ($crawler->count() == 0) {
            throw new UnsupportedLinkException();
        }

        $author = $crawler->first()->getNode(0)->textContent;

        $author = trim($author);

        return $author;
    }

    /**
     * @param Crawler $crawler
     *
     * @return string
     */
    private function resolveTitle(Crawler $crawler)
    {
        $crawler = $crawler->filter('#js-lyric-cnt h1');

        $title = $crawler->first()->getNode(0)->textContent;

        return $title;
    }

    /**
     * @param Crawler $crawler
     *
     * @return string
     */
    private function resolveLyrics(Crawler $crawler)
    {
        $crawler = $crawler->filter('.cnt-letra-trad');

        $lyrics = $crawler->filterXPath('//p/text()')->extract(['_text']);
        $lyrics = array_map(function($item) {
            return trim($item);
        }, $lyrics);
        $lyrics = implode("\n", $lyrics);

        return $lyrics;
    }
}
<?php

namespace Muchacuba\Internauta\Lyrics;

use Cubalider\Navigation\RequestPage;
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
     */
    private function resolveAuthor(Crawler $crawler)
    {
        $crawler = $crawler->filter('#js-lyric-cnt h2');

        $author = $crawler->first()->getNode(0)->textContent;

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
        $crawler = $crawler->filter('article');

        $lyrics = $crawler->first()->html();
        $lyrics = str_replace(['<p>', '</p>', '<br>', '"'], ['', '', "\n", ''], $lyrics);

        return $lyrics;
    }
}
<?php

namespace Muchacuba\Internauta\Lyrics;

use Yosmy\Navigation\RequestPage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     tags: [{name: 'internauta.lyrics.read_lyrics', key: 'metrolyrics'}]
 * })
 */
class MetrolyricsReadLyrics implements ReadLyrics
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
        if (parse_url($link, PHP_URL_HOST) !== 'www.metrolyrics.com') {
            throw new UnsupportedLinkException();
        }

        // Is artist page?
        if (strpos($link, '-lyrics.html') !== false) {
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
        $crawler = $crawler->filter('h2');

        $author = $crawler->first()->getNode(0)->textContent;
        $author = str_replace('Lyrics', '', $author);
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
        $crawler = $crawler->filter('h1');

        $title = $crawler->first()->getNode(0)->textContent;
        $title = str_replace('Lyrics', '', $title);
        $title = trim($title);

        return $title;
    }

    /**
     * @param Crawler $crawler
     *
     * @return string
     *
     * @throws UnsupportedLinkException
     */
    private function resolveLyrics(Crawler $crawler)
    {
        $crawler = $crawler->filter('#lyrics-body-text');

        $lyrics = $crawler->filterXPath('//p/text()')->extract(['_text']);
        $lyrics = array_map('trim', $lyrics);
        $lyrics = implode("\n", $lyrics);

        if (empty($lyrics)) {
            throw new UnsupportedLinkException();
        }

        return $lyrics;
    }
}
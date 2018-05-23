<?php

namespace Muchacuba\Internauta\Lyrics;

use Yosmy\Navigation\RequestPage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     tags: [{name: 'internauta.lyrics.read_lyrics', key: 'azlyrics'}]
 * })
 */
class AzlyricsReadLyrics implements ReadLyrics
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
        // It looks like our server ip was banned.
        // Also, they don't have google cache
        // So temporally it will be disabled from search

        if (parse_url($link, PHP_URL_HOST) !== 'www.azlyrics.com') {
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
        $crawler = $crawler->filter('.lyricsh');

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
        $crawler = $crawler->filter('.container .text-center b');

        $title = $crawler->eq(1)->getNode(0)->textContent;
        $title = str_replace('"', '', $title);

        return $title;
    }

    /**
     * @param Crawler $crawler
     *
     * @return string
     */
    private function resolveLyrics(Crawler $crawler)
    {
        $crawler = $crawler->filter('.container .text-center div');

        $lyrics = $crawler->eq(7)->getNode(0)->textContent;
        $lyrics = trim($lyrics);

        return $lyrics;
    }
}
<?php

namespace Muchacuba\Internauta\Lyrics;

use Cubalider\Navigation\RequestPage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     tags: [{name: 'internauta.lyrics.read_lyrics', key: 'genius'}]
 * })
 */
class GeniusReadLyrics implements ReadLyrics
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
        if (parse_url($link, PHP_URL_HOST) !== 'genius.com') {
            throw new UnsupportedLinkException();
        }

        $crawler = $this->requestPage->request($link);

        $author = $this->resolveAuthor($crawler);

        $title = $this->resolveTitle($crawler);

        try {
            $lyrics = $this->resolveLyrics($crawler);
        } catch (UnsupportedLinkException $e) {
            throw $e;
        }

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
        $crawler = $crawler->filter('.lyrics p');

        $lyrics = $crawler->filterXPath('//text()')->extract(['_text']);
        $lyrics = array_map('trim', $lyrics);
        $lyrics = implode("\n", $lyrics);

        if (!$lyrics) {
            throw new UnsupportedLinkException();
        }

        return $lyrics;
    }
}
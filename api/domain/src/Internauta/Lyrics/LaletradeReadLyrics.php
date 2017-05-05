<?php

namespace Muchacuba\Internauta\Lyrics;

use Cubalider\Navigation\RequestPage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{name: 'internauta.lyrics.read_lyrics', key: 'laletrade'}]
 * })
 */
class LaletradeReadLyrics implements ReadLyrics
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
        if (parse_url($link, PHP_URL_HOST) !== 'laletrade.com') {
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
        $crawler = $crawler
            ->filter('h1');

        $author = $crawler->first()->getNode(0)->textContent;
        $author = trim($author);
        $author = substr($author, strrpos($author, ' de ') + 4);

        return $author;
    }

    /**
     * @param Crawler $crawler
     *
     * @return string
     */
    private function resolveTitle(Crawler $crawler)
    {
        $crawler = $crawler
            ->filter('h1');

        $title = $crawler->first()->getNode(0)->textContent;
        $title = trim($title);
        $title = substr($title, 0, strrpos($title, ' de '));
        $title = str_replace('Letra de ', '', $title);

        return $title;
    }

    /**
     * @param Crawler $crawler
     *
     * @return string
     */
    private function resolveLyrics(Crawler $crawler)
    {
        $crawler = $crawler->filter('.itemFullText p:not(:first-child)');

        $lyrics = $crawler->filterXPath('//p/text()')->extract(['_text']);
        $lyrics = array_map(function($item) {
            return trim($item);
        }, $lyrics);
        $lyrics = implode("\n", $lyrics);

        return $lyrics;
    }
}
<?php

namespace Muchacuba\Internauta\Lyrics;

use Cubalider\Navigation\RequestPage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     tags: [{name: 'internauta.lyrics.read_lyrics', key: 'musica'}]
 * })
 */
class MusicaReadLyrics implements ReadLyrics
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
        if (parse_url($link, PHP_URL_HOST) !== 'www.musica.com') {
            throw new UnsupportedLinkException();
        }

        parse_str(parse_url($link, PHP_URL_QUERY), $query);
        if (!isset($query['id']) && !isset($query['letra'])) {
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
            ->filter('table.rst table tr:nth-child(2) a:nth-child(3)');

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
        $crawler = $crawler
            ->filter('h2');

        $title = $crawler->text();
        $title = str_replace("LETRA '", '', $title);
        $title = substr($title, 0, strlen($title) - 1);
        $title = strtolower($title);
        $title = ucfirst($title);

        return $title;
    }

    /**
     * @param Crawler $crawler
     *
     * @return string
     */
    private function resolveLyrics(Crawler $crawler)
    {
        $crawler = $crawler->filter('table.ijk table table table td > p');

        $lyrics = $crawler->filterXPath('//p/font/text()')->extract(['_text']);
        $lyrics = array_map(function($item) {
            return trim($item);
        }, $lyrics);
        $lyrics = implode("\n", $lyrics);
        $lyrics = trim($lyrics);

        return $lyrics;
    }
}
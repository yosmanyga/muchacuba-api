<?php

namespace Muchacuba\Internauta\Lyrics;

use Cubalider\Navigation\RequestPage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     tags: [{name: 'internauta.lyrics.read_lyrics', key: 'musixmatch'}]
 * })
 */
class MusixmatchReadLyrics implements ReadLyrics
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
        if (parse_url($link, PHP_URL_HOST) !== 'www.musixmatch.com') {
            throw new UnsupportedLinkException();
        }

        $link = sprintf('http://webcache.googleusercontent.com/search?q=cache:%s', $link);

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
        $title = str_replace('Songtexte', '', $title);
        $title = str_replace('Letra y traducción', '', $title);
        $title = str_replace('Letra', '', $title);
        $title = str_replace('Testo', '', $title);
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
        if ($crawler->filter('.mxm-empty__title')->count() !== 0) {
            throw new UnsupportedLinkException();
        }

        $englishCrawler = $crawler->filter('.mxm-lyrics__content');

        if ($englishCrawler->count() !== 0) {
            $lyrics = $englishCrawler->filterXPath('//p//text()')->extract(['_text']);
            $lyrics = array_map(function($item) {
                return trim($item);
            }, $lyrics);
            $lyrics = implode("\n", $lyrics);

            return $lyrics;
        }

        $translatedCrawler = $crawler->filter('.mxm-translatable-line-readonly');

        if ($translatedCrawler->count() !== 0) {
            $lyrics = $translatedCrawler->filterXPath('//text()')->extract(['_text']);
            $lyrics = array_map(function($item) {
                return trim($item);
            }, $lyrics);

            $englishLyrics = [];
            $translatedLyrics = [];
            foreach ($lyrics as $i => $line) {
                if ($i % 2 == 0) {
                    $englishLyrics[] = $line;
                } else {
                    $translatedLyrics[] = $line;
                }
            }

            $lyrics = sprintf(
                "%s\n\n%s",
                implode("\n", $englishLyrics),
                implode("\n", $translatedLyrics)
            );

            return $lyrics;
        }

        throw new UnsupportedLinkException();
    }
}
<?php

namespace Muchacuba\Internauta\Lyrics;

use Cubalider\Navigation\RequestPage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     tags: [{name: 'internauta.lyrics.read_lyrics', key: 'songs_traducidas'}]
 * })
 */
class SongsTraducidasReadLyrics implements ReadLyrics
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
        if (parse_url($link, PHP_URL_HOST) !== 'www.songstraducidas.com') {
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
        $crawler = $crawler->filter('h1.title');

        $author = $crawler->first()->getNode(0)->textContent;
        $author = explode('-', $author);
        $author = $author[0];
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
        $crawler = $crawler->filter('h1.title');

        $title = $crawler->first()->getNode(0)->textContent;
        $title = explode('-', $title);
        $title = $title[1];
        $title = trim($title);

        return $title;
    }

    /**
     * @param Crawler $crawler
     *
     * @return string
     */
    private function resolveLyrics(Crawler $crawler)
    {
        $crawler = $crawler->filter('#two-columns article');

        /* Big hack, because there is a problem in the html with an unclosed div */

        $lyrics = [];
        for ($i = 0; $i <= 1; $i++) {
            $text = $crawler->eq($i)->html();
            $text = substr($text, 0, strpos($text, '<script'));
            $text = preg_replace(
                [
                    '/<h1(.*?)>(.*?)<\/h1>/',
                    '/<div>/',
                    '/<p(.*?)>(.*?)<\/p>/',
                    '/<p>/',
                ],
                '',
                $text
            );
            $text = str_replace("\r\n", '', $text);
            $text = str_replace('<br>', "\n", $text);
            $text = trim($text);

            $lyrics[] = $text;
        }

        $lyrics = implode("\n\n", $lyrics);

        return $lyrics;
    }
}
<?php

namespace Muchacuba\Internauta\Lyrics;

use Yosmy\Navigation\RequestPage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     tags: [{name: 'internauta.lyrics.read_lyrics', key: 'vagavolume'}]
 * })
 */
class VagavolumeReadLyrics implements ReadLyrics
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
        // It could be also a link from amazon aws,
        // like http://vagalume-524411837.us-east-1.elb.amazonaws.com
        if (strpos(parse_url($link, PHP_URL_HOST), 'vagalume') === false) {
            throw new UnsupportedLinkException();
        }

        $crawler = $this->requestPage->request($link);

        // Is a lyrics page?
        if ($crawler->filter('#lyr_original')->count() == 0) {
            throw new UnsupportedLinkException();
        }

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
        $crawler = $crawler->filter('#header p');

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
        $crawler = $crawler->filter('#header h1');

        $title = $crawler->first()->getNode(0)->textContent;
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
        $crawler = $crawler->filter('#lyr_original div');

        $lyrics = $crawler->first()->html();
        $lyrics = str_replace('<br>', "\n", $lyrics);
        $lyrics = trim($lyrics);

        return $lyrics;
    }
}
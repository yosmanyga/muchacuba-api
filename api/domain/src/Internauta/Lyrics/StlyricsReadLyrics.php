<?php

namespace Muchacuba\Internauta\Lyrics;

use Muchacuba\Internauta\CreateClient;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{name: 'internauta.lyrics.read_lyrics', key: 'stlyrics'}]
 * })
 */
class StlyricsReadLyrics implements ReadLyrics
{
    /**
     * @var CreateClient
     */
    private $createClient;

    /**
     * @param CreateClient $createClient
     */
    public function __construct(CreateClient $createClient)
    {
        $this->createClient = $createClient;
    }

    /**
     * {@inheritdoc}
     */
    public function read($link)
    {
        if (parse_url($link, PHP_URL_HOST) !== 'www.stlyrics.com') {
            throw new UnsupportedLinkException();
        }

        $client = $this->createClient->create();

        $crawler = $client->request('GET', $link);

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
        $crawler = $crawler->filter('#content h1');

        $author = $crawler->first()->getNode(0)->textContent;
        $author = explode('-', $author);
        $author = end($author);
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
        $crawler = $crawler->filter('#content h1');

        $title = $crawler->first()->getNode(0)->textContent;
        $title = explode('-', $title);
        $title = current($title);
        $title = str_replace(' lyrics', '', $title);
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
        $crawler = $crawler->filter('#page div');

        $lyrics = $crawler->each(function(Crawler $crawler) {
            return $crawler->first()->getNode(0)->textContent;
        });
        $lyrics = implode("\n", $lyrics);

        return $lyrics;
    }
}
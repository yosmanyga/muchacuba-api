<?php

namespace Muchacuba\Internauta\Lyrics;

use Muchacuba\Internauta\CreateClient;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{name: 'internauta.lyrics.read_lyrics', key: 'genius'}]
 * })
 */
class GeniusReadLyrics implements ReadLyrics
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
        if (parse_url($link, PHP_URL_HOST) !== 'genius.com') {
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
     */
    private function resolveLyrics(Crawler $crawler)
    {
        $crawler = $crawler->filter('.lyrics p');

        $lyrics = $crawler->filterXPath('//a/text()')->extract(['_text']);
        $lyrics = array_map('trim', $lyrics);
        $lyrics = implode("\n", $lyrics);

        return $lyrics;
    }
}
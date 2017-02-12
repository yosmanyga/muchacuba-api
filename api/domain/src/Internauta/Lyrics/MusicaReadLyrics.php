<?php

namespace Muchacuba\Internauta\Lyrics;

use Muchacuba\Internauta\CreateClient;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{name: 'internauta.lyrics.read_lyrics', key: 'musica'}]
 * })
 */
class MusicaReadLyrics implements ReadLyrics
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
        if (parse_url($link, PHP_URL_HOST) !== 'www.musica.com') {
            throw new UnsupportedLinkException();
        }

        parse_str(parse_url($link, PHP_URL_QUERY), $query);
        if (!isset($query['letra'])) {
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
            ->filter('table.rst table tr:nth-child(2) a:nth-child(4)');

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
        $crawler = $crawler->filter('table.ijk table table table td > p');

        $lyrics = $crawler->filterXPath('//p/font/text()')->extract(['_text']);
        $lyrics = array_map(function($item) {
            return trim($item);
        }, $lyrics);
        $lyrics = implode("\n", $lyrics);

        return $lyrics;
    }
}
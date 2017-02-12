<?php

namespace Muchacuba\Internauta\Lyrics;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class DelegateReadLyrics implements ReadLyrics
{
    /**
     * @var ReadLyrics[]
     */
    private $readLyricsServices;

    /**
     * @param ReadLyrics[] $readLyricsServices
     *
     * @di\arguments({
     *     readLyricsServices: '#internauta.lyrics.read_lyrics',
     * })
     */
    public function __construct(
        $readLyricsServices = []
    )
    {
        $this->readLyricsServices = $readLyricsServices;
    }

    /**
     * {@inheritdoc}
     */
    public function read($link)
    {
        foreach ($this->readLyricsServices as $readLyrics) {
            try {
                return $readLyrics->read($link);
            } catch (UnsupportedLinkException $e) {
                continue;
            }
        }

        throw new UnsupportedLinkException();
    }
}
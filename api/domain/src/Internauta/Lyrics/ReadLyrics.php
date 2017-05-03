<?php

namespace Muchacuba\Internauta\Lyrics;

interface ReadLyrics
{
    /**
     * @param string $link
     *
     * @return [string, string, string]
     *
     * @throws UnsupportedLinkException
     */
    public function read($link);
}
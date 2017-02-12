<?php

namespace Muchacuba\Internauta\Lyrics;

interface ReadLyrics
{
    /**
     * @param string $link
     *
     * @return [string, string, string]
     */
    public function read($link);
}
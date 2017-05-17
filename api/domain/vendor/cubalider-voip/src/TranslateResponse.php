<?php

namespace Cubalider\Voip;

interface TranslateResponse
{
    /**
     * @param mixed  $response
     * @param string $cid
     * @param string $from
     *
     * @return array
     *
     * @throws UnsupportedResponseException
     */
    public function translate($response, $cid, $from);
}
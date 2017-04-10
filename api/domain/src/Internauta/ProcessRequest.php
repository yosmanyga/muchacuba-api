<?php

namespace Muchacuba\Internauta;

interface ProcessRequest
{
    /**
     * @param string $sender
     * @param string $receptor
     * @param string $subject
     * @param string $body
     *
     * @return ProcessResult
     *
     * @throws UnsupportedRequestException
     */
    public function process($sender, $receptor, $subject, $body);

    /**
     * @return string
     */
    public function help();
}
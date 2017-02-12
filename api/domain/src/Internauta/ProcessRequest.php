<?php

namespace Muchacuba\Internauta;

interface ProcessRequest
{
    /**
     * @param string $sender
     * @param string $recipient
     * @param string $subject
     * @param string $body
     *
     * @return ProcessResult
     *
     * @throws UnsupportedRequestException
     */
    public function process($sender, $recipient, $subject, $body);

    /**
     * @return string
     */
    public function help();
}
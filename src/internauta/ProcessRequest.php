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
     * @return int
     */
    public function support($sender, $recipient, $subject, $body);

    /**
     * @param string $sender
     * @param string $recipient
     * @param string $subject
     * @param string $body
     *
     * @return ProcessResult
     */
    public function process($sender, $recipient, $subject, $body);

    /**
     * @return string
     */
    public function help();
}
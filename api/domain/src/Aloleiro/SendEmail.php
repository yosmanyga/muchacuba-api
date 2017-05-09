<?php

namespace Muchacuba\Aloleiro;

use Mailgun\Mailgun;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class SendEmail
{
    /**
     * @var string
     */
    private $mailgunApiKey;

    /**
     * @param string $mailgunApiKey
     *
     * @di\arguments({
     *     mailgunApiKey: '%mailgun_api_key%'
     * })
     */
    public function __construct(
        $mailgunApiKey
    )
    {
        $this->mailgunApiKey = $mailgunApiKey;
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $text
     */
    public function send($to, $subject, $text)
    {
        $mg = Mailgun::create($this->mailgunApiKey);

        $mg->messages()->send(
            'muchacuba.com',
            [
                'from' => 'Holapana <sistema@holapana.com>',
                'to' => $to,
                'subject' => $subject,
                'text' => $text
            ]
        );
    }
}

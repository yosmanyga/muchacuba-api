<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\SendEmail as BaseSendEmail;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class SendEmail
{
    /**
     * @var BaseSendEmail
     */
    private $sendEmail;

    /**
     * @param BaseSendEmail $sendEmail
     */
    public function __construct(BaseSendEmail $sendEmail)
    {
        $this->sendEmail = $sendEmail;
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $text
     */
    public function send($to, $subject, $text)
    {
        $this->sendEmail->send(
            'Holapana <sistema@holapana.com>',
            $to,
            $subject,
            $text
        );
    }
}

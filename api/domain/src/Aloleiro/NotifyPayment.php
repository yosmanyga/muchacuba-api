<?php

namespace Muchacuba\Aloleiro;

use Mailgun\Mailgun;
use Cubalider\Facebook\PickProfile as PickFacebookProfile;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class NotifyPayment
{
    /**
     * @var PickFacebookProfile
     */
    private $pickFacebookProfile;

    /**
     * @var string
     */
    private $mailgunApiKey;

    /**
     * @param PickFacebookProfile $pickFacebookProfile
     * @param string              $mailgunApiKey
     *
     * @di\arguments({
     *     mailgunApiKey: '%mailgun_api_key%'
     * })
     */
    public function __construct(
        PickFacebookProfile $pickFacebookProfile,
        $mailgunApiKey
    )
    {
        $this->pickFacebookProfile = $pickFacebookProfile;
        $this->mailgunApiKey = $mailgunApiKey;
    }

    /**
     * @param string $uniqueness
     * @param string $reference
     */
    public function notify($uniqueness, $reference)
    {
        $profile = $this->pickFacebookProfile->pick($uniqueness);

        (new Mailgun($this->mailgunApiKey))->sendMessage(
            'muchacuba.com',
            [
                'from' => 'Holapana <sistema@holapana.com>',
                'to' => 'yosmanyga@gmail.com,admin@jimenezsolutions.com.ve',
                'subject' => 'Pago de un cliente en Holapana',
                'text' => sprintf(
                    "El cliente %s (%s) ha hecho un pago en Holapana.\r\nEl código de la transferencia es %s",
                    $profile->getName(),
                    $profile->getEmail(),
                    $reference
                )
            ]
        );
    }
}

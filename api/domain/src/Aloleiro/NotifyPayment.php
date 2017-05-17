<?php

namespace Muchacuba\Aloleiro;

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
    private $sendEmail;

    /**
     * @param PickFacebookProfile $pickFacebookProfile
     * @param SendEmail           $sendEmail
     */
    public function __construct(
        PickFacebookProfile $pickFacebookProfile,
        SendEmail $sendEmail
    )
    {
        $this->pickFacebookProfile = $pickFacebookProfile;
        $this->sendEmail = $sendEmail;
    }

    /**
     * @param string $uniqueness
     * @param string $reference
     */
    public function notify($uniqueness, $reference)
    {
        $profile = $this->pickFacebookProfile->pick($uniqueness);

        $this->sendEmail->send(
            'yosmanyga@gmail.com,admin@jimenezsolutions.com.ve',
            'Pago de un cliente en Holapana',
            sprintf(
                "El cliente %s (%s) ha hecho un pago en Holapana.\r\nEl código de la transferencia es %s",
                $profile->getName(),
                $profile->getEmail(),
                $reference
            )
        );
    }
}

<?php

namespace Muchacuba\Internauta\Advertising;

use Muchacuba\Internauta\Advertising\Profile\ManageStorage;
use Muchacuba\Internauta\SendEmail;

/**
 * @di\service({
 *     private: true
 * })
 */
class AdvertiseUser
{
    /**
     * @var SendEmail
     */
    private $sendEmail;

    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param SendEmail     $sendEmail
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        SendEmail $sendEmail,
        ManageStorage $manageStorage
    )
    {
        $this->sendEmail = $sendEmail;
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param Profile $profile
     * @param Email   $email
     */
    public function advertise(
        Profile $profile,
        Email $email
    ) {
        $this->sendEmail->send(
            'Equipo Muchacuba <equipo@muchacuba.com>',
            $profile->getEmail(),
            $email->getSubject(),
            $email->getBody()
        );

        $this->manageStorage->connect()->updateOne(
            ['_id' => $profile->getUser()],
            ['$push' => [
                'advertisements' => new Advertisement($email->getId(), time())
            ]]
        );
    }
}
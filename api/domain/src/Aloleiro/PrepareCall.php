<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;
use Muchacuba\Aloleiro\Call\InvalidDataException;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PrepareCall
{
    /**
     * @var PickProfile
     */
    private $pickProfile;

    /**
     * @var PickPhone
     */
    private $pickPhone;

    /**
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @param PickProfile       $pickProfile
     * @param PickPhone         $pickPhone
     * @param ManageCallStorage $manageCallStorage
     */
    public function __construct(
        PickProfile $pickProfile,
        PickPhone $pickPhone,
        ManageCallStorage $manageCallStorage
    )
    {
        $this->pickProfile = $pickProfile;
        $this->pickPhone = $pickPhone;
        $this->manageCallStorage = $manageCallStorage;
    }

    /**
     * @param string $uniqueness
     * @param string $from
     * @param string $to
     *
     * @throws InvalidDataException
     * @throws NonExistentPhoneException
     */
    public function prepare($uniqueness, $from, $to)
    {
        if (!ctype_digit($to)) {
            throw new InvalidDataException(
                InvalidDataException::FIELD_TO
            );
        }

        $to = '+' . $to;

        $profile = $this->pickProfile->pick($uniqueness);

        // Verify number
        try {
            $this->pickPhone->pick($from, $profile->getBusiness());
        } catch (NonExistentPhoneException $e) {
            throw $e;
        }

        $this->manageCallStorage->connect()->insertOne(new Call(
            uniqid(),
            $profile->getBusiness(),
            $from,
            $to,
            []
        ));
    }
}
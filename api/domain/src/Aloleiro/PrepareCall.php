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
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @param PickProfile       $pickProfile
     * @param ManageCallStorage $manageCallStorage
     */
    public function __construct(
        PickProfile $pickProfile,
        ManageCallStorage $manageCallStorage
    )
    {
        $this->pickProfile = $pickProfile;
        $this->manageCallStorage = $manageCallStorage;
    }

    /**
     * @param string $uniqueness
     * @param string $from
     * @param string $to
     *
     * @throws InvalidDataException
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
    
        $this->manageCallStorage->connect()->insertOne(new Call(
            uniqid(),
            $profile->getBusiness(),
            $from,
            $to,
            []
        ));
    }
}
<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Phone\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PickPhone
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        ManageStorage $manageStorage
    ) {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param string $number
     * @param string $business
     *
     * @return Phone
     *
     * @throws NonExistentPhoneException
     */
    public function pick($number, $business)
    {
        /** @var Phone $phone */
        $phone = $this->manageStorage->connect()
            ->findOne([
                '_id' => $number,
                'business' => $business
            ]);

        if (is_null($phone)) {
            throw new NonExistentPhoneException();
        }

        return $phone;
    }
}

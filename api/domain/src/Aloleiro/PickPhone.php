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
     * @param Business $business
     * @param string   $number
     *
     * @return Phone
     *
     * @throws NonExistentPhoneException
     */
    public function pick(Business $business, $number)
    {
        /** @var Phone $phone */
        $phone = $this->manageStorage->connect()
            ->findOne([
                '_id' => $number,
                'business' => $business->getId()
            ]);

        if (is_null($phone)) {
            throw new NonExistentPhoneException();
        }

        return $phone;
    }
}

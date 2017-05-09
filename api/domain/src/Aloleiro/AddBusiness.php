<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Business\InvalidDataException;
use Muchacuba\Aloleiro\Business\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class AddBusiness
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
     * @param int         $profitPercent
     * @param float       $balance
     * @param string      $name
     * @param string      $address
     *
     * @return Business
     *
     * @throws InvalidDataException
     */
    public function add(
        $profitPercent,
        $balance,
        $name,
        $address
    ) {
        if ($profitPercent != (string)(int) $profitPercent) {
            throw new InvalidDataException(InvalidDataException::FIELD_PROFIT_PERCENT);
        }

        if ($profitPercent < 0) {
            throw new InvalidDataException(InvalidDataException::FIELD_PROFIT_PERCENT);
        }

        $business = new Business(
            uniqid(),
            $balance,
            floatval($profitPercent),
            $name,
            $address
        );

        $this->manageStorage->connect()->insertOne($business);

        return $business;
    }
}
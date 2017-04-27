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
     * @param int         $balance
     * @param int         $profitPercent
     * @param string      $name
     * @param string      $address
     * @param string|null $id
     *
     * @return string
     *
     * @throws InvalidDataException
     */
    public function add(
        $balance,
        $profitPercent,
        $name,
        $address,
        $id = null)
    {
        if ($balance != floatval($balance)) {
            throw new InvalidDataException(InvalidDataException::FIELD_BALANCE);
        }

        $id = $id ?: uniqid();

        $this->manageStorage->connect()->insertOne(new Business(
            $id,
            floatval($balance),
            $profitPercent,
            $name,
            $address
        ));

        return $id;
    }
}
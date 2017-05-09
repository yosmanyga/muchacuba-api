<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Business\InvalidDataException;
use Muchacuba\Aloleiro\Business\ManageStorage as ManageBusinessStorage;
use MongoDB\UpdateResult;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class UpdateBusiness
{
    /**
     * @var ManageBusinessStorage
     */
    private $manageBusinessStorage;

    /**
     * @param ManageBusinessStorage $manageBusinessStorage
     */
    public function __construct(
        ManageBusinessStorage $manageBusinessStorage
    )
    {
        $this->manageBusinessStorage = $manageBusinessStorage;
    }

    /**
     * @param Business $business
     * @param string   $profitPercent
     *
     * @throws InvalidDataException
     * @throws \Exception
     */
    public function update(Business $business, $profitPercent)
    {
        if (!filter_var($profitPercent, FILTER_VALIDATE_INT)) {
            throw new InvalidDataException(
                InvalidDataException::FIELD_PROFIT_PERCENT
            );
        }

        /** @var UpdateResult $result */
        $result = $this->manageBusinessStorage->connect()->updateOne(
            [
                '_id' => $business->getId(),
            ],
            ['$set' => [
                'profitPercent' => $profitPercent
            ]]
        );

        if ($result->getMatchedCount() === 0) {
            throw new \Exception(sprintf("Business with id '%s' does not exist", $business->getId()));
        }
    }
}
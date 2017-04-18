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
     * @var PickProfile
     */
    private $pickProfile;

    /**
     * @var ManageBusinessStorage
     */
    private $manageBusinessStorage;

    /**
     * @param PickProfile           $pickProfile
     * @param ManageBusinessStorage $manageBusinessStorage
     */
    public function __construct(
        PickProfile $pickProfile,
        ManageBusinessStorage $manageBusinessStorage
    )
    {
        $this->pickProfile = $pickProfile;
        $this->manageBusinessStorage = $manageBusinessStorage;
    }

    /**
     * @param string $uniqueness
     * @param string $profitPercent
     * @param string $currencyExchange
     *
     * @throws InvalidDataException
     * @throws \Exception
     */
    public function update($uniqueness, $profitPercent, $currencyExchange)
    {
        if (!filter_var($profitPercent, FILTER_VALIDATE_INT)) {
            throw new InvalidDataException(
                InvalidDataException::FIELD_PROFIT_PERCENT
            );
        }

        if (!filter_var($currencyExchange, FILTER_VALIDATE_FLOAT)) {
            throw new InvalidDataException(
                InvalidDataException::FIELD_CURRENCY_EXCHANGE
            );
        }

        $profile = $this->pickProfile->pick($uniqueness);

        /** @var UpdateResult $result */
        $result = $this->manageBusinessStorage->connect()->updateOne(
            [
                '_id' => $profile->getBusiness(),
            ],
            ['$set' => [
                'profitPercent' => $profitPercent,
                'currencyExchange' => $currencyExchange,
            ]]
        );

        if ($result->getMatchedCount() === 0) {
            throw new \Exception(sprintf("Business with id '%s' does not exist", $profile->getBusiness()));
        }
    }
}
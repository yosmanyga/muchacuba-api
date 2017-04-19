<?php

namespace Muchacuba\Aloleiro;

use MongoDB\UpdateResult;
use Muchacuba\Aloleiro\Country\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class UpdateCountry
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
    )
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param string $name
     * @param float  $currencyExchange
     *
     * @throws \Exception
     */
    public function update($name, $currencyExchange)
    {
        /** @var UpdateResult $result */
        $result = $this->manageStorage->connect()->updateOne(
            [
                'name' => $name
            ],
            ['$set' => [
                'currencyExchange' => $currencyExchange
            ]]
        );

        if ($result->getMatchedCount() == 0) {
            if ($result->getMatchedCount() === 0) {
                throw new \Exception(sprintf('Country with name "%s" does not exist', $name));
            }
        }
    }
}
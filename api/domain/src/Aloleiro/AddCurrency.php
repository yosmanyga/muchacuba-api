<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Currency\ManageStorage as ManageCurrencyStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class AddCurrency
{
    /**
     * @var ManageCurrencyStorage
     */
    private $manageCurrencyStorage;

    /**
     * @param ManageCurrencyStorage  $manageCurrencyStorage
     */
    public function __construct(
        ManageCurrencyStorage $manageCurrencyStorage
    )
    {
        $this->manageCurrencyStorage = $manageCurrencyStorage;
    }

    /**
     * @param string $code
     * @param float  $value
     */
    public function add($code, $value)
    {
        $this->manageCurrencyStorage->connect()->insertOne([
            '_id' => $code,
            'value' => $value
        ]);
    }
}
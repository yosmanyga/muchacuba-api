<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Currency\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PickCurrency
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
     * @return string
     */
    public function pickEUR()
    {
        return $this->pick('EUR')->value;
    }

    /**
     * @return string
     */
    public function pickVEF()
    {
        return $this->pick('VEF')->value;
    }

    /**
     * @param string $code
     *
     * @return \stdClass
     */
    private function pick($code)
    {
        $currency = $this->manageStorage->connect()
            ->findOne([
                '_id' => $code,
            ]);

        return $currency;
    }
}

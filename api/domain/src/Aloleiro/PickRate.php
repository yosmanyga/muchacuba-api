<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Rate\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PickRate
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
     * @param string $name
     *
     * @return Rate
     */
    public function pick($name)
    {
        /** @var Rate $rate */
        $rate = $this->manageStorage->connect()
            ->findOne([
                'countryName' => $name,
            ]);

        return $rate;
    }
}

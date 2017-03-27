<?php

namespace Cubalider\Geo;

use Cubalider\Geo\Profile\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class UpdateProfile
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
     * @param string $uniqueness
     * @param string $lat
     * @param string $lng
     */
    public function update(
        $uniqueness,
        $lat,
        $lng
    ) {
        $this->manageStorage->connect()->updateOne(
            ['_id' => $uniqueness],
            [
                '$set' => [
                    'lat' => $lat,
                    'lng' => $lng
                ]
            ]
        );
    }
}
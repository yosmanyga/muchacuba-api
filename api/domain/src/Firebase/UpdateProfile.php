<?php

namespace Muchacuba\Firebase;

use Muchacuba\Firebase\Profile\ManageStorage;

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
     * @param string $token
     */
    public function update(
        $uniqueness,
        $token
    ) {
        $this->manageStorage->connect()->updateOne(
            ['_id' => $uniqueness],
            [
                '$set' => ['token' => $token]
            ]
        );
    }
}
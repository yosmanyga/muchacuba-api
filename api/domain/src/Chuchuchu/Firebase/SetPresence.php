<?php

namespace Muchacuba\Chuchuchu\Firebase;

use Muchacuba\Chuchuchu\Firebase\Profile\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class SetPresence
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
     * @param string $id
     * @param string $token
     */
    public function set(
        $id,
        $token
    ) {
        $this->manageStorage->connect()->updateOne(
            ['_id' => $id],
            [
                '$set' => ['token' => $token]
            ]
        );
    }
}
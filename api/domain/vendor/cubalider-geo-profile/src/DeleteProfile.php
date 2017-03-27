<?php

namespace Cubalider\Geo;

use Cubalider\Geo\Profile\ManageStorage;
use MongoDB\DeleteResult;

/**
 * @author Yosmany Garcia <yosmanyga@gmail.com>
 *
 * @di\service({deductible: true})
 */
class DeleteProfile
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage $manageStorage
     */
    public function __construct(ManageStorage $manageStorage)
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * Deletes the profile with given uniqueness.
     *
     * @param string $uniqueness
     *
     * @throws NonExistentProfileException
     */
    public function delete($uniqueness)
    {
        /** @var DeleteResult $result */
        $result = $this->manageStorage->connect()->deleteOne([
            '_id' => $uniqueness,
        ]);

        if ($result->getDeletedCount() === 0) {
            throw new NonExistentProfileException();
        }
    }
}

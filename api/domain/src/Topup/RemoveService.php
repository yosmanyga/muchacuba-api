<?php

namespace Muchacuba\Topup;

use MongoDB\DeleteResult;
use Muchacuba\Topup\Service\ManageStorage as ManageServiceStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class RemoveService
{
    /**
     * @var ManageServiceStorage
     */
    private $manageServiceStorage;

    /**
     * @param ManageServiceStorage $manageServiceStorage
     */
    public function __construct(
        ManageServiceStorage $manageServiceStorage
    )
    {
        $this->manageServiceStorage = $manageServiceStorage;
    }

    /**
     * @param string $id
     *
     * @throws NonExistentServiceException
     */
    public function remove($id)
    {
        /** @var DeleteResult $result */
        $result = $this->manageServiceStorage->connect()->deleteOne([
            '_id' => $id,
        ]);

        if ($result->getDeletedCount() === 0) {
            throw new NonExistentServiceException();
        }
    }
}
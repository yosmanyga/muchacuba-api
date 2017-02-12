<?php

namespace Muchacuba\Internauta;

use Muchacuba\Internauta\Log\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class DeleteLogGroup
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
     * @param string $id
     *
     * @throws NonExistentLogException
     */
    public function delete($id)
    {
        $result = $this->manageStorage->connect()->deleteMany([
            'payload.id' => $id
        ]);

        if ($result->getDeletedCount() === 0) {
            throw new NonExistentLogException($id);
        }
    }
}
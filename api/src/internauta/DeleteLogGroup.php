<?php

namespace Muchacuba\Internauta;

use Muchacuba\Internauta\Log\ManageStorage;

/**
 * @di\service()
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
     * @http\resolution({method: "POST", path: "/internauta/delete-log-group"})
     *
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
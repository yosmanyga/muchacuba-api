<?php

namespace Muchacuba\Aloleiro;

use MongoDB\DeleteResult;
use Muchacuba\Aloleiro\AdminApproval\ManageStorage as ManageApprovalStorage;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class RemoveAdminApproval
{
    /**
     * @var ManageApprovalStorage
     */
    private $manageApprovalStorage;

    /**
     * @param ManageApprovalStorage  $manageApprovalStorage
     */
    public function __construct(
        ManageApprovalStorage $manageApprovalStorage
    )
    {
        $this->manageApprovalStorage = $manageApprovalStorage;
    }

    /**
     * @param string $email
     *
     * @throws NonExistentApprovalException
     */
    public function remove($email)
    {
        /** @var DeleteResult $result */
        $result = $this->manageApprovalStorage->connect()->deleteOne([
            '_id' => $email,
        ]);

        if ($result->getDeletedCount() === 0) {
            throw new NonExistentApprovalException();
        }
    }
}
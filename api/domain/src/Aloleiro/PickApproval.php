<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Approval\ManageStorage;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class PickApproval
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
     * @param string $email
     *
     * @return Approval
     *
     * @throws NonExistentApprovalException
     */
    public function pick($email)
    {
        /** @var Approval $approval */
        $approval = $this->manageStorage->connect()
            ->findOne([
                '_id' => $email,
            ]);

        if (is_null($approval)) {
            throw new NonExistentApprovalException();
        }

        return $approval;
    }
}

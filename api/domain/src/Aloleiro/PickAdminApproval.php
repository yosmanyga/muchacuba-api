<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\AdminApproval\ManageStorage;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class PickAdminApproval
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
     * @return AdminApproval
     *
     * @throws NonExistentAdminApprovalException
     */
    public function pick($email)
    {
        /** @var AdminApproval $adminApproval */
        $adminApproval = $this->manageStorage->connect()
            ->findOne([
                '_id' => $email,
            ]);

        if (is_null($adminApproval)) {
            throw new NonExistentAdminApprovalException();
        }

        return $adminApproval;
    }
}

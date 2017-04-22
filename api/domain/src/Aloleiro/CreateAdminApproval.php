<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\AdminApproval\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CreateAdminApproval
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
     * @param string $role
     */
    public function create(
        $email,
        $role
    )
    {
        $this->manageStorage->connect()->insertOne(new AdminApproval(
            $email,
            $role
        ));
    }
}
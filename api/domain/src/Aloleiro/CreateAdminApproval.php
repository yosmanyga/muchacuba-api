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
     * @var string
     */
    private $adminEmail;

    /**
     * @param ManageStorage $manageStorage
     * @param string        $adminEmail
     *
     * @di\arguments({
     *     adminEmail: "%aloleiro_admin_email%",
     * })
     */
    public function __construct(
        ManageStorage $manageStorage,
        $adminEmail
    ) {
        $this->manageStorage = $manageStorage;
        $this->adminEmail = $adminEmail;
    }

    /**
     */
    public function create()
    {
        $this->manageStorage->connect()->insertOne(new AdminApproval(
            $this->adminEmail,
            'aloleiro_admin'
        ));
    }
}
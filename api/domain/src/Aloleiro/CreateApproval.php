<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Approval\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CreateApproval
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
     * @param string   $email
     * @param string   $business
     * @param string[] $roles
     */
    public function create(
        $email,
        $business,
        $roles
    )
    {
        $this->manageStorage->connect()->insertOne(new Approval(
            $email,
            $business,
            $roles
        ));
    }
}
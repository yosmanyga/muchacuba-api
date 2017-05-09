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
     * @param Business $business
     * @param string   $email
     * @param string[] $roles
     */
    public function create(
        $business,
        $email,
        $roles
    )
    {
        $this->manageStorage->connect()->insertOne(new Approval(
            $email,
            $business->getId(),
            $roles
        ));
    }
}
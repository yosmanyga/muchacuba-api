<?php

namespace Muchacuba;

use Cubalider\Privilege\NonExistentProfileException as NonExistentPrivilegeProfileException;
use Cubalider\Privilege\Profile\ManageStorage as ManagePrivilegeStorage;
use MongoDB\UpdateResult;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PromoteUser
{
    /**
     * @var ManagePrivilegeStorage
     */
    private $managePrivilegeStorage;

    /**
     * @param ManagePrivilegeStorage $managePrivilegeStorage
     */
    public function __construct(
        ManagePrivilegeStorage $managePrivilegeStorage
    )
    {
        $this->managePrivilegeStorage = $managePrivilegeStorage;
    }

    /**
     * @param string $uniqueness
     * @param string $role
     *
     * @throws NonExistentPrivilegeProfileException
     * @throws \Exception
     */
    public function promote($uniqueness, $role)
    {
        /** @var UpdateResult $result */
        $result = $this->managePrivilegeStorage->connect()->updateOne(
            ['_id' => $uniqueness],
            ['$push' => ['roles' => $role]]
        );

        if ($result->getMatchedCount() == 0) {
            throw new \Exception();
        }
    }
}

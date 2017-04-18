<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Business\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PickBusiness
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
    )
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param string $id
     *
     * @return Business
     *
     * @throws NonExistentBusinessException
     */
    public function pick($id)
    {
        /** @var Business $business */
        $business = $this->manageStorage->connect()->findOne([
            '_id' => $id,
        ]);

        if (is_null($business)) {
            throw new NonExistentBusinessException();
        }

        return $business;
    }
}

<?php

namespace Muchacuba\Aloleiro;

use Cubalider\Voip\QueryCall as BaseQueryCall;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class QueryCall
{
    /**
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @var BaseQueryCall
     */
    private $queryCall;

    /**
     * @param ManageCallStorage $manageCallStorage
     * @param BaseQueryCall     $queryCall
     */
    public function __construct(
        ManageCallStorage $manageCallStorage,
        BaseQueryCall $queryCall
    )
    {
        $this->manageCallStorage = $manageCallStorage;
        $this->queryCall = $queryCall;
    }

    /**
     * @param Business $business
     * @param string   $id
     *
     * @throws NonExistentCallException
     */
    public function query(Business $business, $id)
    {
        $call = $this->manageCallStorage->connect()->findOne([
            'business' => $business->getId(),
            'instances.id' => $id
        ]);

        if (is_null($call)) {
            throw new NonExistentCallException();
        }

        $this->queryCall->query($id);
    }
}
<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Event\ManageStorage as ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class RegisterEvent
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
     * @param array  $payload
     */
    public function register($payload)
    {
        $this->manageStorage->connect()->insertOne(new Event(
            uniqid(),
            $payload
        ));
    }
}
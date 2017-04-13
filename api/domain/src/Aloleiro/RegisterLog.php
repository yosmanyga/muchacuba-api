<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Event\ManageStorage as ManageStorage;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class RegisterLog
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
     * @param string $type
     * @param array  $payload
     */
    public function register($type, $payload)
    {
        $this->manageStorage->connect()->insertOne(new Log(
            uniqid(),
            $type,
            $payload
        ));
    }
}
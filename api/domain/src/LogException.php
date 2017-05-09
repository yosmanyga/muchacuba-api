<?php

namespace Muchacuba;

use Muchacuba\Exception\ManageStorage;
use Symsonte\Http\Server\LogException as BaseLogException;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class LogException implements BaseLogException
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
     * {@inheritdoc}
     */
    public function log(
        \Exception $e
    ) {
        $exception = new Exception(
            uniqid(),
            $e->getMessage(),
            $e->getCode(),
            $e->getFile(),
            $e->getLine()
        );

        $this->manageStorage->connect()->insertOne($exception);
    }
}

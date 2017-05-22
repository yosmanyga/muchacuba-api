<?php

namespace Muchacuba;

use Muchacuba\Exception\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class LogException
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var SendEmail
     */
    private $sendEmail;

    /**
     * @param ManageStorage $manageStorage
     * @param SendEmail     $sendEmail
     */
    public function __construct(
        ManageStorage $manageStorage,
        SendEmail $sendEmail
    ) {
        $this->manageStorage = $manageStorage;
        $this->sendEmail = $sendEmail;
    }

    /**
     * @param \Exception|\Throwable $e
     */
    public function log($e)
    {
        $exception = new Exception(
            uniqid(),
            $e->getMessage(),
            $e->getCode(),
            $e->getFile(),
            $e->getLine()
        );

        $this->manageStorage->connect()->insertOne($exception);

        $this->sendEmail->send(
            'Muchacuba <system@muchacuba.com>',
            'yosmanyga@gmail.com',
            'Exception',
            print_r($exception, true)
        );
    }
}

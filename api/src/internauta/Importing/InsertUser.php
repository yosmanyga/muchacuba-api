<?php

namespace Muchacuba\Internauta\Importing;

use Muchacuba\Internauta\Importing\Cubamessenger\ExistentEmailException;
use Muchacuba\Internauta\Importing\User\ManageStorage;
use MongoDB\Driver\Exception\BulkWriteException;

/**
 * @di\service({
 *     private: true
 * })
 */
class InsertUser
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
     * @param string      $email
     * @param string      $mobile
     * @param string|null $id
     *
     * @return string
     *
     * @throws ExistentEmailException
     */
    public function insert(
        $email,
        $mobile,
        $id = null
    ) {
        $id = $id ?: uniqid();

        try {
            $this->manageStorage->connect()->insertOne(new User(
                $id,
                $email,
                $mobile
            ));
        } catch (BulkWriteException $e) {
            if ($e->getCode() == 'E11000') {
                if (strpos($e->getMessage(), 'email_1') !== false) {
                    throw new ExistentEmailException();
                }
            }

            throw $e;
        }

        return $id;
    }
}
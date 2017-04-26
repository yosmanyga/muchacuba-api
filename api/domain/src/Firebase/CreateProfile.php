<?php

namespace Muchacuba\Firebase;

use MongoDB\Driver\Exception\BulkWriteException;
use Muchacuba\Firebase\Profile\ManageStorage;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class CreateProfile
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
     * @param string $uniqueness
     * @param string $token
     *
     * @throws ExistentProfileException
     */
    public function create($uniqueness, $token)
    {
        try {
            $this->manageStorage->connect()->insertOne(new Profile(
                $uniqueness,
                $token
            ));
        } catch (BulkWriteException $e) {
            if ($e->getWriteResult()->getWriteErrors()[0]->getCode() == 11000) {
                throw new ExistentProfileException();
            }

            throw $e;
        }
    }
}
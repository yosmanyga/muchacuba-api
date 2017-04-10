<?php

namespace Muchacuba\Aloleiro;

use MongoDB\Driver\Exception\BulkWriteException;
use Muchacuba\Aloleiro\Profile\ManageStorage;

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
     * @param string        $uniqueness
     * @param string[]|null $phones
     *
     * @throws ExistentProfileException
     */
    public function create($uniqueness, array $phones = [])
    {
        try {
            $this->manageStorage->connect()->insertOne(new Profile(
                $uniqueness,
                $phones
            ));
        } catch (BulkWriteException $e) {
            if ($e->getWriteResult()->getWriteErrors()[0]->getCode() == 11000) {
                throw new ExistentProfileException();
            }

            throw $e;
        }
    }
}
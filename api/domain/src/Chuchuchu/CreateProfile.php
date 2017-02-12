<?php

namespace Muchacuba\Chuchuchu;

use MongoDB\Driver\Exception\BulkWriteException;
use Muchacuba\Chuchuchu\Profile\ManageStorage;

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
     * @param string   $uniqueness
     * @param string[] $contacts
     *
     * @throws ExistentProfileException
     */
    public function create($uniqueness, $contacts)
    {
        try {
            $this->manageStorage->connect()->insertOne(new Profile(
                $uniqueness,
                $contacts
            ));
        } catch (BulkWriteException $e) {
            if ($e->getWriteResult()->getWriteErrors()[0]->getCode() == 11000) {
                throw new ExistentProfileException();
            }

            throw $e;
        }
    }
}
<?php

namespace Cubalider\Geo;

use Cubalider\Geo\Profile\ManageStorage;
use MongoDB\Driver\Exception\BulkWriteException;

/**
 * @author Yosmany Garcia <yosmanyga@gmail.com>
 *
 * @di\service({deductible: true})
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
     * Creates a profile.
     *
     * @param string $uniqueness
     * @param string $lat
     * @param string $lng
     *
     * @throws ExistentProfileException
     *
     * @return string
     */
    public function create($uniqueness, $lat, $lng)
    {
        try {
            $this->manageStorage->connect()->insertOne(
                new Profile($uniqueness, $lat, $lng)
            );
        } catch (BulkWriteException $e) {
            if ($e->getWriteResult()->getWriteErrors()[0]->getCode() == 11000) {
                throw new ExistentProfileException();
            }

            throw $e;
        }

        return $uniqueness;
    }
}

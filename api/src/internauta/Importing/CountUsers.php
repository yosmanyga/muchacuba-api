<?php

namespace Muchacuba\Internauta\Importing;

use Muchacuba\Internauta\Importing\User\ManageStorage;

/**
 * @di\service()
 */
class CountUsers
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage $manageStorage
     */
    public function __construct(ManageStorage $manageStorage)
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->manageStorage->connect()->count();
    }
}
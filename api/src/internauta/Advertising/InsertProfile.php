<?php

namespace Muchacuba\Internauta\Advertising;

use Muchacuba\Internauta\Advertising\Profile\ManageStorage;
use Muchacuba\Internauta\User;

/**
 * @di\service()
 */
class InsertProfile
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
     * @param string               $user
     * @param string               $email
     * @param Advertisement[]|null $advertisements
     */
    public function insert(
        $user,
        $email,
        $advertisements = null
    ) {
        $this->manageStorage->connect()->insertOne(new Profile(
            $user,
            $email,
            $advertisements
        ));
    }
}
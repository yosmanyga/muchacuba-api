<?php

namespace Muchacuba\Firebase;

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
     */
    public function create($uniqueness, $token)
    {
        $this->manageStorage->connect()->insertOne(new Profile(
            $uniqueness,
            $token
        ));
    }
}
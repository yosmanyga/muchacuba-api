<?php

namespace Cubalider\Geo;

use Cubalider\Geo\Profile\ManageStorage;

/**
 * @author Yosmany Garcia <yosmanyga@gmail.com>
 *
 * @di\service({deductible: true})
 */
class CollectProfiles
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
     * Collects profiles.
     *
     * @return Profiles
     */
    public function collect()
    {
        $profiles = new Profiles($this->manageStorage->connect()->find());

        return $profiles;
    }
}

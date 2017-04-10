<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Phone\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectPhones
{
    /**
     * @var PickProfile
     */
    private $pickProfile;

    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param PickProfile   $pickProfile
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        PickProfile $pickProfile,
        ManageStorage $manageStorage
    )
    {
        $this->pickProfile = $pickProfile;
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param string $uniqueness
     *
     * @return Phone[]
     */
    public function collect($uniqueness)
    {
        $profile = $this->pickProfile->pick($uniqueness);

        $phones = $this->manageStorage->connect()->find([
            '_id' => ['$in' => $profile->getPhones()]
        ]);

        return iterator_to_array($phones);
    }
}

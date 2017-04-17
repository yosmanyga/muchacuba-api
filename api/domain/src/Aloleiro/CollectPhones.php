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
    private $PickProfile;

    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param PickProfile $PickProfile
     * @param ManageStorage       $manageStorage
     */
    public function __construct(
        PickProfile $PickProfile,
        ManageStorage $manageStorage
    )
    {
        $this->pickProfile = $PickProfile;
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
            'business' => $profile->getBusiness()
        ]);

        return iterator_to_array($phones);
    }
}

<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Call\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectPreparedCalls
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
     * @return ClientCall[]
     */
    public function collect($uniqueness)
    {
        $profile = $this->pickProfile->pick($uniqueness);

        $calls = $this->manageStorage->connect()->find(
            [
                'business' => $profile->getBusiness(),
                'instances' => []
            ],
            [
                'sort' => [
                    '_id' => -1
                ]
            ]
        );

        return iterator_to_array($calls);
    }
}

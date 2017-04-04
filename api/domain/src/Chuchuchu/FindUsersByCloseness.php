<?php

namespace Muchacuba\Chuchuchu;

use Cubalider\Facebook\Profile\ManageStorage as FacebookManageStorage;
use Cubalider\Facebook\Profile as FacebookProfile;
use Muchacuba\Chuchuchu\User\InitAlgoliaIndex;
use Muchacuba\User;
use Cubalider\Geo\PickProfile as PickGeoProfile;
use Firebase\Factory;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class FindUsersByCloseness
{
    /**
     * @var PickGeoProfile
     */
    private $pickGeoProfile;

    /**
     * @var InitAlgoliaIndex
     */
    private $initAlgoliaIndex;

    /**
     * @var FacebookManageStorage
     */
    private $facebookManageStorage;

    /**
     * @param PickGeoProfile        $pickGeoProfile
     * @param InitAlgoliaIndex      $initAlgoliaIndex
     * @param FacebookManageStorage $facebookManageStorage
     */
    public function __construct(
        PickGeoProfile $pickGeoProfile,
        InitAlgoliaIndex $initAlgoliaIndex,
        FacebookManageStorage $facebookManageStorage
    )
    {
        $this->pickGeoProfile = $pickGeoProfile;
        $this->initAlgoliaIndex = $initAlgoliaIndex;
        $this->facebookManageStorage = $facebookManageStorage;
    }

    /**
     * @param string $uniqueness
     *
     * @return User[]
     */
    public function find($uniqueness)
    {
        $geoProfile = $this->pickGeoProfile->pick($uniqueness);

        $users = $this->searchOnAlgolia(
            $geoProfile->getLat(),
            $geoProfile->getLng()
        );

        $users = $this->prepareUsers($users);

        return $users;
    }

    /**
     * @param string   $lat
     * @param string   $lng
     * @param int|null $radius
     *
     * @return string[]
     */
    private function searchOnAlgolia($lat, $lng, $radius = null)
    {
        $radius = ($radius ?: 1000) * 1000;

        $args = [
            'aroundLatLng' => sprintf('%s, %s', $lat, $lng),
            'aroundRadius' => $radius,
            'facets' => '*',
        ];

        $index = $this->initAlgoliaIndex->init();
        $index
            ->setSettings([
                'attributesForFaceting' => ['destinations'],
            ]);

        $res = $index->search('', $args);

        if (!isset($res['hits'])) {
            return [];
        }

        $users = [];
        foreach ($res['hits'] as $hit) {
            $users[] = $hit['objectID'];
        }

        return $users;
    }

    /**
     * @param string[] $users
     *
     * @return User[]
     */
    private function prepareUsers($users)
    {
        $profiles = [];

        /** @var FacebookProfile[] $facebookProfiles */
        $facebookProfiles = $this->facebookManageStorage->connect()->find([
            '_id' => ['$in' => $users]
        ]);
        foreach ($facebookProfiles as $facebookProfile) {
            $profiles[$facebookProfile->getUniqueness()] = new User(
                $facebookProfile->getUniqueness(),
                $facebookProfile->getName(),
                $facebookProfile->getEmail(),
                '',
                $facebookProfile->getPicture()
            );
        }

        // Need array_values to return non-associative array
        // Otherwise it's converted to an object by json
        return array_values($profiles);
    }
}

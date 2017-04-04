<?php

namespace Muchacuba\Chuchuchu\User;

use Cubalider\Geo\UpdateProfile as UpdateGeoProfile;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class SetGeo
{
    /**
     * @var UpdateGeoProfile
     */
    private $updateGeoProfile;

    /**
     * @var InitAlgoliaIndex
     */
    private $initAlgoliaIndex;

    /**
     * @param UpdateGeoProfile      $updateGeoProfile
     * @param InitAlgoliaIndex          $initAlgoliaIndex
     */
    public function __construct(
        UpdateGeoProfile $updateGeoProfile,
        InitAlgoliaIndex $initAlgoliaIndex
    ) {
        $this->updateGeoProfile = $updateGeoProfile;
        $this->initAlgoliaIndex = $initAlgoliaIndex;
    }

    /**
     * @param string $uniqueness
     * @param string $lat
     * @param string $lng
     */
    public function set(
        $uniqueness,
        $lat,
        $lng
    ) {
        $this->updateGeoProfile->update($uniqueness, $lat, $lng);

        $this->indexOnAlgolia($uniqueness, $lat, $lng);
    }

    /**
     * Adds an object on algolia with given data.
     *
     * @param string $uniqueness
     * @param string $lat
     * @param string $lng
     */
    private function indexOnAlgolia(
        $uniqueness,
        $lat,
        $lng
    )
    {
        $index = $this->initAlgoliaIndex->init();

        $res = $index->addObject([
            'objectID' => $uniqueness,
            '_geoloc' => [
                'lat' => $lat,
                'lng' => $lng
            ]
        ]);
        $index->waitTask($res['taskID']);
    }
}
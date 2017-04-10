<?php

namespace Muchacuba\Chuchuchu;

use Cubalider\Unique\Uniquenesses;
use Cubalider\Unique\CollectUniquenesses;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectUsers
{
    /**
     * @var CollectUniquenesses
     */
    private $collectUniquenesses;

    /**
     * @var EnrichUsers
     */
    private $enrichUsers;

    /**
     * @param CollectUniquenesses $collectUniquenesses
     * @param EnrichUsers         $enrichUsers
     */
    public function __construct(
        CollectUniquenesses $collectUniquenesses,
        EnrichUsers $enrichUsers
    )
    {
        $this->collectUniquenesses = $collectUniquenesses;
        $this->enrichUsers = $enrichUsers;
    }

    /**
     * @return User[]
     */
    public function collect()
    {
        /** @var Uniquenesses $uniquenesses */
        $uniquenesses = $this->collectUniquenesses->collect();

        $users = [];
        foreach ($uniquenesses as $uniqueness) {
            $users[] = $uniqueness->getId();
        }

        return $this->enrichUsers->enrich($users);
    }
}

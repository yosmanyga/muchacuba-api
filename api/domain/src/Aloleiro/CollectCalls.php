<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Call\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectCalls
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
    )
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param Business|null $business
     *
     * @return Call[]
     */
    public function collect(Business $business = null)
    {
        $criteria = [];

        if ($business !== null) {
            $criteria['business'] = $business->getId();
        }

        return $this->find(
            $criteria
        );
    }

    /**
     * @param Business|null $business
     *
     * @return Call[]
     */
    public function collectPrepared(Business $business = null)
    {
        $criteria = [];

        if ($business !== null) {
            $criteria['business'] = $business->getId();
        }

        $criteria['instances'] = [];

        return $this->find(
            $criteria,
            [
                'sort' => [
                    '_id' => -1
                ]
            ]
        );
    }

    /**
     * @param Business|null $business
     *
     * @return Call[]
     */
    public function collectInProgress(Business $business = null)
    {
        $criteria = [];

        if ($business !== null) {
            $criteria['business'] = $business->getId();
        }

        $criteria['instances'] = [
            'start' => ['$ne' => null],
            'end' => ['$eq' => null]
        ];

        return $this->find(
            $criteria,
            [
                'sort' => [
                    '_id' => -1
                ]
            ]
        );
    }

    /**
     * @param array      $criteria
     * @param array|null $options
     *
     * @return Call[]
     */
    private function find($criteria, $options = [])
    {
        $calls = $this->manageStorage->connect()->find(
            $criteria,
            $options
        );

        return iterator_to_array($calls);
    }
}

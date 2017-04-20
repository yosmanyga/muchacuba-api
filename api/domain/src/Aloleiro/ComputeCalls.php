<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\UTCDateTime;
use Muchacuba\Aloleiro\Call\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ComputeCalls
{
    const GROUP_BY_DAY = 1;
    const GROUP_BY_MONTH = 2;
    const GROUP_BY_YEAR = 3;

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
     * @param string $from
     * @param string $to
     * @param int    $group
     *
     * @return array
     *
     * @throws \Exception
     */
    public function compute($uniqueness, $from, $to, $group)
    {
        $profile = $this->pickProfile->pick($uniqueness);
        
        switch ($group) {
            case self::GROUP_BY_DAY:
                $instanceTimestamp = [
                    'year' => ['$year' => '$instances.timestamp'],
                    'month' => ['$month' => '$instances.timestamp'],
                    'day' => ['$dayOfMonth' => '$instances.timestamp']
                ];

                break;
            case self::GROUP_BY_MONTH:
                $instanceTimestamp = [
                    'year' => ['$year' => '$instances.timestamp'],
                    'month' => ['$month' => '$instances.timestamp']
                ];

                break;
            case self::GROUP_BY_YEAR:
                $instanceTimestamp = [
                    'year' => ['$year' => '$instances.timestamp']
                ];

                break;
            default:
                throw new \Exception();
        }

        $response = $this->manageStorage->connect()
            ->aggregate(
                [
                    ['$match' => [
                        'business' => $profile->getBusiness(),
                        'instances.timestamp' => [
                            '$gte' => new UTCDatetime($from * 1000),
                            '$lt' => new UTCDatetime($to * 1000),
                        ]
                    ]],
                    ['$unwind' => '$instances'],
                    ['$group' => [
                        '_id' => $instanceTimestamp,
                        'duration' => [
                            '$sum' => '$instances.duration'
                        ],
                        'systemPurchase' => [
                            '$sum' => '$instances.systemPurchase'
                        ],
                        'systemSale' => [
                            '$sum' => '$instances.systemSale'
                        ],
                        'systemProfit' => [
                            '$sum' => '$instances.systemProfit'
                        ],
                        'businessPurchase' => [
                            '$sum' => '$instances.businessPurchase'
                        ],
                        'businessSale' => [
                            '$sum' => '$instances.businessSale'
                        ],
                        'businessProfit' => [
                            '$sum' => '$instances.businessProfit'
                        ],
                        'total' => [
                            '$sum' => 1
                        ]
                    ]],
                    ['$sort' => ['_id' => 1]]
                ]
            );

        $stats = [];
        foreach ($response as $item) {
            $item = json_decode(json_encode($item), true);
            $item = array_merge(
                $item,
                $item['_id']
            );
            unset($item['_id']);

            $stats[] = $item;
        }

        return $stats;
    }
}
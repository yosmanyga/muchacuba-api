<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\UTCDateTime;
use Muchacuba\Aloleiro\Call\Instance;
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
     * @param string|null   $from
     * @param string|null   $to
     * @param int|null      $group
     *
     * @return array
     */
    public function compute(Business $business = null, $from = null, $to = null, $group = null)
    {
        $criteria = [];

        if (!is_null($business)) {
            $criteria['business'] = $business->getId();
        }

        if (!is_null($from)) {
            $criteria['instances.localStart']['$gte'] = new UTCDateTime($from * 1000);
        }

        if (!is_null($to)) {
            $criteria['instances.localStart']['$lt'] = new UTCDatetime($to * 1000);
        }

        $criteria['instances.result'] = Instance::RESULT_DID_SPEAK;

        switch ($group) {
            case self::GROUP_BY_DAY:
                $instanceStart = [
                    'year' => ['$year' => '$instances.localStart'],
                    'month' => ['$month' => '$instances.localStart'],
                    'day' => ['$dayOfMonth' => '$instances.localStart']
                ];

                break;
            case self::GROUP_BY_MONTH:
                $instanceStart = [
                    'year' => ['$year' => '$instances.localStart'],
                    'month' => ['$month' => '$instances.localStart']
                ];

                break;
            case self::GROUP_BY_YEAR:
                $instanceStart = [
                    'year' => ['$year' => '$instances.localStart']
                ];

                break;
            default:
                $instanceStart = [
                    'year' => ['$year' => '$instances.localStart'],
                    'month' => ['$month' => '$instances.localStart'],
                    'day' => ['$dayOfMonth' => '$instances.localStart']
                ];
        }

        $response = $this->manageStorage->connect()
            ->aggregate(
                [
                    ['$unwind' => '$instances'],
                    ['$project' => [
                        'business' => 1,
                        'instances.duration' => 1,
                        'instances.systemPurchase' => 1,
                        'instances.systemSale' => 1,
                        'instances.systemProfit' => 1,
                        'instances.businessPurchase' => 1,
                        'instances.businessSale' => 1,
                        'instances.businessProfit' => 1,
                        'instances.result' => 1,
                        // Field with the date in local timezone
                        'instances.localStart' => [
                            '$subtract' => [
                                '$instances.start',
                                4 * 60 * 60 * 1000 // -4 is America/Caracas
                            ]
                        ]
                    ]],
                    ['$match' => $criteria],
                    ['$group' => [
                        '_id' => $instanceStart,
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
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
            $criteria['instances.start']['$gte'] = new UTCDateTime($from * 1000);
        }

        if (!is_null($to)) {
            $criteria['instances.start']['$lt'] = new UTCDatetime($to * 1000);
        }

        $criteria['instances.result'] = Instance::RESULT_DID_SPEAK;

        switch ($group) {
            case self::GROUP_BY_DAY:
                $instanceStart = [
                    'year' => ['$year' => '$instances.start'],
                    'month' => ['$month' => '$instances.start'],
                    'day' => ['$dayOfMonth' => '$instances.start']
                ];

                break;
            case self::GROUP_BY_MONTH:
                $instanceStart = [
                    'year' => ['$year' => '$instances.start'],
                    'month' => ['$month' => '$instances.start']
                ];

                break;
            case self::GROUP_BY_YEAR:
                $instanceStart = [
                    'year' => ['$year' => '$instances.start']
                ];

                break;
            default:
                $instanceStart = [
                    'year' => ['$year' => '$instances.start'],
                    'month' => ['$month' => '$instances.start'],
                    'day' => ['$dayOfMonth' => '$instances.start']
                ];
        }

        $response = $this->manageStorage->connect()
            ->aggregate(
                [
                    ['$unwind' => '$instances'],
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
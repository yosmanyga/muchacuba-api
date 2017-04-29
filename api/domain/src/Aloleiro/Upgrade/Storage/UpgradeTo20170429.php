<?php

namespace Muchacuba\Aloleiro\Upgrade\Storage;

use Cubalider\Call\Provider\Log;
use MongoDB\BSON\UTCDateTime;
use Muchacuba\Aloleiro\Call;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;
use Cubalider\Call\Provider\Log\ManageStorage as ManageLogStorage;
use Muchacuba\Aloleiro\Call\Instance;
use Muchacuba\Aloleiro\Upgrade\ManageStorage as ManageUpgradeStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class UpgradeTo20170429
{
    const ID = '20170429';

    /**
     * @var ManageUpgradeStorage
     */
    private $manageUpgradeStorage;

    /**
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @var ManageLogStorage
     */
    private $manageLogStorage;

    /**
     * @param ManageUpgradeStorage $manageUpgradeStorage
     * @param ManageCallStorage    $manageCallStorage
     * @param ManageLogStorage     $manageLogStorage
     */
    public function __construct(
        ManageUpgradeStorage $manageUpgradeStorage,
        ManageCallStorage $manageCallStorage,
        ManageLogStorage $manageLogStorage
    )
    {
        $this->manageUpgradeStorage = $manageUpgradeStorage;
        $this->manageCallStorage = $manageCallStorage;
        $this->manageLogStorage = $manageLogStorage;
    }

    /**
     * @return string|null
     */
    public function upgrade()
    {
        $upgrade = $this->manageUpgradeStorage->connect()->findOne();

        if (!is_null($upgrade)) {
            return null;
        }

        /** @var Log[] $logs */
        $logs = $this->manageLogStorage->connect()->find([
            'payload.event' => 'ace'
        ]);

        foreach ($logs as $log) {
            /** @var Call $call */
            $call = $this->manageCallStorage->connect()->findOne([
                'instances.id' => $log->getPayload()['callid']
            ]);

            /** @var Instance[] $instances */
            $instances = array_values(array_filter(
                $call->getInstances(),
                function($instance) use ($log) {
                    return $instance['id'] == $log->getPayload()['callid'];
                }
            ));

            $this->manageCallStorage->connect()->updateOne(
                [
                    'instances.id' => $log->getPayload()['callid']
                ],
                ['$set' => [
                    'instances.$' => new Instance(
                        $instances[0]['id'],
                        new UTCDateTime(
                            strtotime($log->getPayload()['timestamp']) * 1000
                        ),
                        $instances[0]['timestamp'],
                        $instances[0]['duration'],
                        $instances[0]['systemPurchase'],
                        $instances[0]['systemSale'],
                        $instances[0]['systemProfit'],
                        $instances[0]['businessPurchase'],
                        $instances[0]['businessSale'],
                        $instances[0]['businessProfit']
                    )
                ]]
            );
        }

        $this->manageUpgradeStorage->connect()->insertOne([
            '_id' => self::ID
        ]);

        return self::ID;
    }
}
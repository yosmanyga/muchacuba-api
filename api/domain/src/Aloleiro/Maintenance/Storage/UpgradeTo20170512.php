<?php

namespace Muchacuba\Aloleiro\Maintenance\Storage;

use Muchacuba\Aloleiro\Call;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;
use Muchacuba\Aloleiro\Call\Instance;
use Muchacuba\Aloleiro\ImportRates;
use Muchacuba\Aloleiro\Rate\ManageStorage as ManageRateStorage;

/**
 * @di\service({
 *   deductible: true,
 *   tags: [{
 *     name: 'muchacuba.aloleiro.maintenance.upgrade_storage',
 *     key: '20170512'
 *   }]
 * })
 */
class UpgradeTo20170512 implements Upgrade
{
    /**
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @var ManageRateStorage
     */
    private $manageRateStorage;

    /**
     * @var ImportRates
     */
    private $importRates;

    /**
     * @param ManageCallStorage $manageCallStorage
     * @param ManageRateStorage $manageRateStorage
     * @param ImportRates       $importRates
     */
    public function __construct(
        ManageCallStorage $manageCallStorage,
        ManageRateStorage $manageRateStorage,
        ImportRates $importRates
    )
    {
        $this->manageCallStorage = $manageCallStorage;
        $this->manageRateStorage = $manageRateStorage;
        $this->importRates = $importRates;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade($last)
    {
        if ($last != '20170429') {
            return false;
        }

        $this->upgradeCalls();

        $this->upgradeRates();

        return true;
    }

    private function upgradeCalls()
    {
        $this->manageCallStorage->connect()->updateMany(
            [],
            [
                '$set' => [
                    'status' => Call::STATUS_ARCHIVED
                ]
            ]
        );

        $calls = $this->manageCallStorage->connect()->find([], [
            'typeMap' => [
                'root' => 'array',
                'document' => 'array',
                'array' => 'array'
            ]
        ]);
        foreach ($calls as $call) {
            foreach ($call['instances'] as $instance) {
                $this->manageCallStorage->connect()->updateOne(
                    ['instances.id' => $instance['id']],
                    [
                        '$set' => [
                            'instances.$.result' => Instance::RESULT_DID_SPEAK
                        ]
                    ]
                );
            }
        }
    }

    private function upgradeRates()
    {
        $this->manageRateStorage->purge();

        $this->importRates->import();
    }
}
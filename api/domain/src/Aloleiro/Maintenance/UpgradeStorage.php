<?php

namespace Muchacuba\Aloleiro\Maintenance;

use Muchacuba\Aloleiro\Maintenance\Storage\Upgrade;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class UpgradeStorage
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var Upgrade[]
     */
    private $upgradeServices;

    /**
     * @param ManageStorage $manageStorage
     * @param Upgrade[]     $upgradeServices
     *
     * @di\arguments({
     *     upgradeServices: '#muchacuba.aloleiro.maintenance.upgrade_storage'
     * })
     */
    public function __construct(
        ManageStorage $manageStorage,
        array $upgradeServices
    )
    {
        $this->manageStorage = $manageStorage;
        $this->upgradeServices = $upgradeServices;
    }

    /**
     * @return string[]
     */
    public function upgrade()
    {
        $ids = [];

        foreach ($this->upgradeServices as $key => $upgrade) {
            $last = $this->getLastUpgrade();

            $result = $upgrade->upgrade($last);

            if ($result == true) {
                $this->addUpgrade((string) $key);

                $ids[] = $key;
            }
        }

        return array_filter($ids);
    }

    /**
     * @return string|null
     */
    private function getLastUpgrade()
    {
        $upgrade = $this->manageStorage->connect()->findOne(
            [],
            [
                'sort' => [
                    '_id' => -1
                ]
            ]
        );

        if ($upgrade == null) {
            return null;
        }

        return $upgrade->_id;
    }

    /**
     * @param string $id
     */
    private function addUpgrade($id)
    {
        $this->manageStorage->connect()->insertOne(
            ['_id' => $id]
        );
    }
}
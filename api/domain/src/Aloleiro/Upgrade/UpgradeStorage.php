<?php

namespace Muchacuba\Aloleiro\Upgrade;

use Muchacuba\Aloleiro\Upgrade\Storage\UpgradeTo20170429;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class UpgradeStorage
{
    /**
     * @var UpgradeTo20170429
     */
    private $upgradeTo20170429;

    /**
     * @param UpgradeTo20170429 $upgradeTo20170429
     */
    public function __construct(
        UpgradeTo20170429 $upgradeTo20170429
    )
    {
        $this->upgradeTo20170429 = $upgradeTo20170429;
    }

    /**
     * @return string[]
     */
    public function upgrade()
    {
        $ids = [];

        $ids[] = $this->upgradeTo20170429->upgrade();

        return array_filter($ids);
    }
}
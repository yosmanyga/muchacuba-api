<?php

namespace Muchacuba\Aloleiro;

use Cubalider\PurgeStorage;
use Cubalider\PurgeStorages as BasePurgeStorages;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PurgeStorages
{
    /**
     * @var BasePurgeStorages
     */
    private $purgeStorages;

    /**
     * @param PurgeStorage[] $aloleiroPurgeStorageServices
     * @param PurgeStorage[] $cubaliderVoipPurgeStorageServices
     *
     * @di\arguments({
     *     aloleiroPurgeStorageServices:      '#muchacuba.aloleiro.purge_storage',
     *     cubaliderVoipPurgeStorageServices: '#cubalider.voip.purge_storage'
     * })
     */
    public function __construct(
        array $aloleiroPurgeStorageServices,
        array $cubaliderVoipPurgeStorageServices
    )
    {
        $this->purgeStorages = new BasePurgeStorages(array_merge(
            $aloleiroPurgeStorageServices,
            $cubaliderVoipPurgeStorageServices
        ));
    }

    /**
     */
    public function purge()
    {
        $this->purgeStorages->purge();
    }
}
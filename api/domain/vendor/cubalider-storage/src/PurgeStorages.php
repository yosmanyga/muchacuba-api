<?php

namespace Cubalider;

class PurgeStorages
{
    /**
     * @var PurgeStorage[]
     */
    private $purgeStorageServices;

    /**
     * @param PurgeStorage[] $purgeStorageServices
     */
    public function __construct(array $purgeStorageServices)
    {
        $this->purgeStorageServices = $purgeStorageServices;
    }

    /**
     */
    public function purge()
    {
        foreach ($this->purgeStorageServices as $purgeStorageService) {
            $purgeStorageService->purge();
        }
    }
}
<?php

namespace Muchacuba\Cli\Topup;

use Muchacuba\Topup\ImportProviders as DomainImportProviders;

/**
 * @di\command({deductible: true})
 */
class ImportProviders
{
    /**
     * @var DomainImportProviders
     */
    private $importProviders;

    /**
     * @param DomainImportProviders $importProviders
     */
    public function __construct(DomainImportProviders $importProviders)
    {
        $this->importProviders = $importProviders;
    }

    /**
     * @cli\resolution({command: "topup.import_providers"})
     */
    public function import()
    {
        $this->importProviders->import();
    }
}

<?php

namespace Muchacuba\Cli\Topup;

use Muchacuba\Topup\ImportPayloads as DomainImportPayloads;

/**
 * @di\command({deductible: true})
 */
class ImportPayloads
{
    /**
     * @var DomainImportPayloads
     */
    private $importPayloads;

    /**
     * @param DomainImportPayloads $importPayloads
     */
    public function __construct(DomainImportPayloads $importPayloads)
    {
        $this->importPayloads = $importPayloads;
    }

    /**
     * @cli\resolution({command: "topup.import_payloads"})
     */
    public function import()
    {
        $this->importPayloads->import();
    }
}

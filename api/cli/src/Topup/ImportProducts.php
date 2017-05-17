<?php

namespace Muchacuba\Cli\Topup;

use Muchacuba\Topup\ImportProducts as DomainImportProducts;

/**
 * @di\command({deductible: true})
 */
class ImportProducts
{
    /**
     * @var DomainImportProducts
     */
    private $importProducts;

    /**
     * @param DomainImportProducts $importProducts
     */
    public function __construct(DomainImportProducts $importProducts)
    {
        $this->importProducts = $importProducts;
    }

    /**
     * @cli\resolution({command: "topup.import_products"})
     */
    public function import()
    {
        $this->importProducts->import();
    }
}

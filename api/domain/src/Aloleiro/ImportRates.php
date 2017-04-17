<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Rate\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ImportRates
{
    /**
     * @var CollectCountries
     */
    private $collectCountries;

    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param CollectCountries $collectCountries
     * @param ManageStorage    $manageStorage
     */
    public function __construct(
        CollectCountries $collectCountries,
        ManageStorage $manageStorage
    )
    {
        $this->collectCountries = $collectCountries;
        $this->manageStorage = $manageStorage;
    }

    /**
     */
    public function import()
    {
        // Purge to start from scratch

        $this->manageStorage->purge();

        $rates = $this->loadRates();

        foreach ($rates as $rate) {
            $this->manageStorage->connect()->insertOne(new Rate(
                uniqid(),
                $rate['country'],
                $rate['type'],
                $rate['code'],
                $rate['value']
            ));
        }
    }

    /**
     * @return array
     */
    private function loadRates()
    {
        $excel = sprintf('%s/excel.xls', sys_get_temp_dir());

        file_put_contents(
            $excel,
            fopen('https://www.sinch.com/voice-price-list', 'r')
        );

        $objPHPExcel = \PHPExcel_IOFactory::load($excel);

        $rates = [];
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            foreach ($worksheet->getRowIterator() as $i => $row) {
                if ($i == 1) {
                    continue;
                }

                /** @var \PHPExcel_Worksheet_RowCellIterator $rowIterator */
                $rowIterator = $row->getCellIterator();

                $rates[] = [
                    'country' => $rowIterator->seek('A')->current()->getValue(),
                    'type' => $rowIterator->seek('B')->current()->getValue(),
                    'code' => $rowIterator->seek('C')->current()->getValue(),
                    'value' => $rowIterator->seek('D')->current()->getValue(),
                ];
            }
        }

        unlink($excel);

        return $rates;
    }
}
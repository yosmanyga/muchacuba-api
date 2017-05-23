<?php

namespace Cubalider\Voip\Nexmo;

use Cubalider\Voip\Rate;
use Cubalider\Voip\Rate\TranslateCountry;
use Cubalider\Voip\Rate\TranslateNetwork;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class LoadRates
{
    /**
     * @var TranslateCountry
     */
    private $translateCountry;

    /**
     * @var TranslateNetwork
     */
    private $translateNetwork;

    /**
     * @param TranslateCountry $translateCountry
     * @param TranslateNetwork $translateNetwork
     */
    public function __construct(
        TranslateCountry $translateCountry,
        TranslateNetwork $translateNetwork
    )
    {
        $this->translateCountry = $translateCountry;
        $this->translateNetwork = $translateNetwork;
    }

    /**
     * @return Rate[]
     */
    public function load()
    {
        $excel = sprintf('%s/excel.xls', sys_get_temp_dir());

        file_put_contents(
            $excel,
            fopen('https://dashboard.nexmo.com/download_pricing', 'r')
        );

        $objPHPExcel = \PHPExcel_IOFactory::load($excel);

        $worksheet = $objPHPExcel->getSheetByName('Outbound Voice');

        $rates = [];
        foreach ($worksheet->getRowIterator() as $i => $row) {
            if ($i == 1) {
                continue;
            }

            /** @var \PHPExcel_Worksheet_RowCellIterator $rowIterator */
            $rowIterator = $row->getCellIterator();

            $rates[] = new Rate(
                $this->translateCountry->translate(
                    (string) $rowIterator->seek('B')->current()->getValue()
                ),
                $this->translateNetwork->translate(
                    (string) $rowIterator->seek('E')->current()->getValue()
                ),
                (string) $rowIterator->seek('G')->current()->getValue(),
                'EUR'
            );
        }

        unlink($excel);

        return $rates;
    }
}
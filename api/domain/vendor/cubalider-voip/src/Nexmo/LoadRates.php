<?php

namespace Cubalider\Voip\Nexmo;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class LoadRates
{
    /**
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

            $rates[] = [
                'countryCode'  => (string) $rowIterator->seek('A')->current()->getValue(),
                'countryName'  => (string) $rowIterator->seek('B')->current()->getValue(),
                'network'      => (string) $rowIterator->seek('C')->current()->getValue(),
                'networkAlias' => (string) $rowIterator->seek('D')->current()->getValue(),
                'networkName'  => (string) $rowIterator->seek('E')->current()->getValue(),
                'prefix'       => (string) $rowIterator->seek('F')->current()->getValue(),
                'price'        => (string) $rowIterator->seek('G')->current()->getValue(),
            ];
        }

        unlink($excel);

        return $rates;
    }
}
<?php

namespace Muchacuba\Aloleiro;

use Goutte\Client;
use Muchacuba\Aloleiro\Price\ManageStorage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ImportPrices
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        ManageStorage $manageStorage
    )
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     */
    public function import()
    {
        $prices = $this->loadPrices();

        $favorites = [
            'Brasil', 'Canada', 'Chile', 'Colombia', 'Costa Rica', 'Cuba',
            'República Dominicana'
        ];

        $translations = $this->loadCountryTranslations();

        foreach ($prices as $price) {
            $price['country'] = $this->translateCountry($price['country'], $translations);

            $this->manageStorage->connect()->insertOne(new Price(
                uniqid(),
                $price['country'],
                $price['prefix'],
                $price['code'],
                $price['type'],
                $price['value'],
                in_array($price['country'], $favorites)
            ));
        }
    }

    /**
     * @return array
     */
    private function loadPrices()
    {
        $excel = sprintf('%s/excel.xls', sys_get_temp_dir());

        file_put_contents(
            $excel,
            fopen('https://www.sinch.com/voice-price-list', 'r')
        );

        $objPHPExcel = \PHPExcel_IOFactory::load($excel);

        $prices = [];
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            foreach ($worksheet->getRowIterator() as $i => $row) {
                if ($i == 1) {
                    continue;
                }

                /** @var \PHPExcel_Worksheet_RowCellIterator $rowIterator */
                $rowIterator = $row->getCellIterator();

                $prices[] = [
                    'country' => $rowIterator->seek('A')->current()->getValue(),
                    'prefix' => $rowIterator->seek('B')->current()->getValue(),
                    'type' => $rowIterator->seek('C')->current()->getValue(),
                    'code' => $rowIterator->seek('D')->current()->getValue(),
                    'value' => $rowIterator->seek('E')->current()->getValue(),
                ];
            }
        }

        unlink($excel);

        return $prices;
    }

    /**
     * @return array
     */
    private function loadCountryTranslations()
    {
        return (new Client())
            ->request('GET', 'http://www.aleida.net/paises-en.html')
            ->filter('table.sortable tbody tr')
            ->each(function(Crawler $crawler) {
                $crawler = $crawler->filter('td');

                return [
                    'country' => str_replace("\n", '', $crawler->eq(0)->getNode(0)->textContent),
                    'translation' => str_replace("\n", '', $crawler->eq(2)->getNode(0)->textContent)
                ];
            });
    }

    /**
     * @param string $country
     * @param array  $translations
     *
     * @return string
     */
    private function translateCountry($country, $translations)
    {
        foreach ($translations as $translation) {
            if ($translation['country'] == $country) {
                return $translation['translation'];
            }
        }

        return $country;
    }
}
<?php

namespace Muchacuba\Aloleiro;

use Cubalider\Voip\Nexmo\LoadRates;
use Goutte\Client;
use Muchacuba\Aloleiro\Rate\ManageStorage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ImportRates
{
    /**
     * @var LoadRates
     */
    private $loadRates;

    /**
     * @var PickCurrency
     */
    private $pickCurrency;

    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var AddRate
     */
    private $addRate;

    /**
     * @param LoadRates     $loadRates
     * @param PickCurrency  $pickCurrency
     * @param ManageStorage $manageStorage
     * @param AddRate       $addRate
     */
    public function __construct(
        LoadRates $loadRates,
        PickCurrency $pickCurrency,
        ManageStorage $manageStorage, 
        AddRate $addRate
    )
    {
        $this->loadRates = $loadRates;
        $this->pickCurrency = $pickCurrency;
        $this->manageStorage = $manageStorage;
        $this->addRate = $addRate;
    }

    /**
     */
    public function import()
    {
        $rates = $this->loadRates->load();

        $translations = $this->loadCountryTranslations();

        $eurValue = $this->pickCurrency->pickEUR();

        // Purge to start from scratch
        $this->manageStorage->purge();

        foreach ($rates as $rate) {
            $countryTranslation = $this->translate(
                $rate['countryName'],
                $translations
            );

            $network = $this->translateNetwork($rate['networkName']);

            $this->addRate->add(
                $rate['countryName'],
                $countryTranslation,
                $network,
                $rate['prefix'],
                round($rate['price'] / $eurValue, 4)
            );
        }
    }

    /**
     * @return string[]
     */
    private function loadCountryTranslations()
    {
        $items = (new Client())
            ->request('GET', 'https://www.spanishveryeasy.com/countries')
            ->filter('.table1 tr')
            ->each(function(Crawler $crawler) {
                $crawler = $crawler->filter('td');

                // Is a header?
                if ($crawler->count() == 0) {
                    return null;
                }

                $name = $crawler->eq(1)->getNode(0)->textContent;
                $name = trim($name);

                $translation = $crawler->eq(0)->getNode(0)->textContent;
                $translation = trim($translation);

                return [$name, $translation];
            });

        $translations = [];
        foreach ($items as $i => $item) {
            if (!$item) {
                continue;
            }

            $translations[$item[0]] = $item[1];
        }

        return $translations;
    }

    /**
     * @param string $country
     * @param string[] $translations
     *
     * @return string
     */
    private function translate($country, $translations)
    {
        // Some missing countries
        $translations['American Samoa'] = 'Samoa';
        $translations['Anguilla'] = 'Anguila';
        $translations['Antarctica'] = 'Antártida';
        $translations['Aruba'] = 'Aruba';
        $translations['Ascension'] = 'Ascensión';
        $translations['Azerbaijani Republic'] = 'República de Azerbaiyán';
        $translations['Belarus'] = 'Bielorrusia';
        $translations['Bermuda'] = 'Bermudas';
        $translations['Bhutan'] = 'Bután';
        $translations['Bonaire, Sint Eustatius and Saba'] = 'Bonaire, Sint Eustatius and Saba';
        $translations['Bosnia and Herzegovina'] = 'Bosnia y Herzegovina';
        $translations['British Virgin Islands'] = 'Islas Vírgenes Británicas';
        $translations['Brunei Darussalam'] = 'Brunei Darussalam';
        $translations['Cape Verde'] = 'Cabo Verde';
        $translations['Caribbean Netherlands'] = 'Países Bajos del caribe';
        $translations['Cayman Islands'] = 'Islas Caimán';
        $translations['Commonwealth of the Northern Mariana Islands (CNMI)'] = 'Comunidad de las Islas Marianas del Norte (CNMI)';
        $translations['Cote d\'Ivoire'] = 'Costa de Marfil';
        $translations['Curacao'] = 'Curacao';
        $translations['Democratic Republic of the Congo'] = 'República Democrática del Congo';
        $translations['Diego Garcia'] = 'Diego Garcia';
        $translations['Djibouti'] = 'Djibouti';
        $translations['Falkland Islands'] = 'Islas Malvinas';
        $translations['Faroe Islands'] = 'Islas Faroe';
        $translations['Fiji'] = 'Fiyi';
        $translations['French Guiana'] = 'Guayana Francesa';
        $translations['French Polynesia'] = 'Polinesia francesa';
        $translations['Gabonese Republic'] = 'República Gabonesa';
        $translations['Gibraltar'] = 'Gibraltar';
        $translations['Guadeloupe'] = 'Guadeloupe';
        $translations['Guam'] = 'Guam';
        $translations['Hongkong China'] = 'Hong Kong China';
        $translations['Korea (Democratic People\'s Republic of)'] = 'Korea del Norte';
        $translations['Korea (Republic of)'] = 'Korea del Sur';
        $translations['Macao China'] = 'Macao China';
        $translations['Martinique'] = 'Martinica';
        $translations['Mayotte'] = 'Mayotte';
        $translations['Montserrat'] = 'Montserrat';
        $translations['Namibia'] = 'Namibia';
        $translations['Netherlands'] = 'Países Bajos';
        $translations['Netherlands Antilles'] = 'Antillas Holandesas';
        $translations['New Caledonia'] = 'Nueva Caledonia';
        $translations['Palestine'] = 'Palestina';
        $translations['Puerto Rico'] = 'Puerto Rico';
        $translations['Reunion'] = 'Reunión';
        $translations['Russian Federation'] = 'Rusia';
        $translations['Rwandese Republic'] = 'República de Rwanda';
        $translations['Saint Helena'] = 'Santa Elena';
        $translations['Saint Kitts and Nevis'] = 'San Cristóbal y Nieves';
        $translations['Saint Lucia'] = 'Santa Lucía';
        $translations['Saint Pierre and Miquelon'] = 'San Pedro y Miquelón';
        $translations['Sao Tome and Principe'] = 'Santo Tomé y Príncipe';
        $translations['Saudi Arabia'] = 'Arabia Saudita';
        $translations['Sint Maarten'] = 'Sint Maarten';
        $translations['Slovak Republic'] = 'República Eslovaca';
        $translations['Somali'] = 'Somalia';
        $translations['South Sudan'] = 'Sudán del Sur';
        $translations['Syrian Arab Republic'] = 'Siria';
        $translations['Taiwan'] = 'Taiwán';
        $translations['Tajikistan'] = 'Timor Oriental';
        $translations['Timor-Leste'] = 'Timor Oriental';
        $translations['Togolese Republic'] = 'República Togolesa';
        $translations['Tokelau'] = 'Tokelau';
        $translations['Turkmenistan'] = 'Turkmenistán';
        $translations['Turks and Caicos Islands'] = 'Islas Turcas y Caicos';
        $translations['Ukraine'] = 'Ucrania';
        $translations['United States'] = 'Estados Unidos';
        $translations['United States Virgin Islands'] = 'Islas Vírgenes de los Estados Unidos';
        $translations['Wallis and Futuna'] = 'Wallis y Futuna';
        $translations['Vatican City State'] = 'Vaticano';

        if (isset($translations[$country])) {
            return $translations[$country];
        }

        return $country;
    }

    /**
     * @param string $network
     *
     * @return string
     */
    private function translateNetwork($network)
    {
        return str_replace(
            ['Landline'],
            ['Fijo'],
            $network
        );
    }
}
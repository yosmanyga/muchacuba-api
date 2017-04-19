<?php

namespace Muchacuba\Aloleiro;

use MongoDB\UpdateResult;
use Goutte\Client;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class UpdateVenezuelanCurrencyExchange
{
    /**
     * @var UpdateCountry
     */
    private $updateCountry;

    /**
     * @param UpdateCountry $updateCountry
     */
    public function __construct(
        UpdateCountry $updateCountry
    )
    {
        $this->updateCountry = $updateCountry;
    }

    /**
     * @throws \Exception
     */
    public function update()
    {
        $value = (new Client())
            ->request(
                'GET',
                'http://d1fbzr3krofnqb.cloudfront.net/wp-content/themes/Newsmag/indicadores.php'
            )
            ->filter('table')
            ->eq(3)
            ->filter('tr')
            ->eq(2)
            ->filter('td')
            ->eq(1)
            ->filter('h2')
            ->first()
            ->getNode(0)
            ->textContent;
        
        $value = str_replace(['BsF', ',', ' '], [], $value);

        $this->updateCountry->update('Venezuela', $value);
    }
}
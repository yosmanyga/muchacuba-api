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
     * @var UpdateRate
     */
    private $updateRate;

    /**
     * @param UpdateRate $updateRate
     */
    public function __construct(
        UpdateRate $updateRate
    )
    {
        $this->updateRate = $updateRate;
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

        $this->updateRate->update('Venezuela', $value);
    }
}
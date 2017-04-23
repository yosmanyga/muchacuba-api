<?php

namespace Muchacuba\Aloleiro;

use GuzzleHttp\Client;

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
        $value = (string) (new Client())
            ->request(
                'GET',
                'https://dxj1e0bbbefdtsyig.woldrssl.net/custom/rate.js'
            )
            ->getBody();

        $value = str_replace('var dolartoday =', '', $value);
        $value = json_decode($value, true);
        $value = $value['USD']['transfer_cucuta'];

        $this->updateRate->update('Venezuela', $value);
    }

//    /**
//     * @throws \Exception
//     */
//    public function update()
//    {
//        $value = (new Client())
//            ->request(
//                'GET',
//                'http://d1fbzr3krofnqb.cloudfront.net/wp-content/themes/Newsmag/indicadores.php'
//            )
//            ->filter('table')
//            ->eq(3)
//            ->filter('tr')
//            ->eq(2)
//            ->filter('td')
//            ->eq(1)
//            ->filter('h2')
//            ->first()
//            ->getNode(0)
//            ->textContent;
//
//        $value = str_replace(['BsF', ',', ' '], [], $value);
//
//        $this->updateRate->update('Venezuela', $value);
//    }
}
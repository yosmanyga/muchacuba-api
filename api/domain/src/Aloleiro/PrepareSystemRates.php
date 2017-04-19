<?php

namespace Muchacuba\Aloleiro;

use Dompdf\Dompdf;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PrepareSystemRates
{
    /**
     * @var CollectSystemRates
     */
    private $collectSystemRates;

    /**
     * @var PickCountry
     */
    private $pickCountry;

    /**
     * @param CollectSystemRates $collectSystemRates
     * @param PickCountry        $pickCountry
     */
    public function __construct(
        CollectSystemRates $collectSystemRates,
        PickCountry $pickCountry
    )
    {
        $this->collectSystemRates = $collectSystemRates;
        $this->pickCountry = $pickCountry;
    }

    /**
     * @return string
     */
    public function prepare()
    {
        $file = sprintf('%s/precios.pdf', sys_get_temp_dir());

        $country = $this->pickCountry->pick('Venezuela');

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml('<strong>Listado de precios</strong>');

        $html = '<table cellpadding="10">';
        $html .= '<tr><th>País</th><th>Tipo</th><th>Precio</th></th>';
        $rates = $this->collectSystemRates->collect();
        foreach ($rates as $rate) {
            $html .= sprintf(
                '<tr><td>%s</td><td>%s</td><td>%s</td></tr>',
                $rate->getCountry(),
                $rate->getType(),
                sprintf('%s Bf', round($rate->getSale() * $country->getCurrencyExchange()))
            );
        }
        $html .= '</table>';

        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream($file);

        return $file;
    }
}

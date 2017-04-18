<?php

namespace Muchacuba\Aloleiro;

use Dompdf\Dompdf;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PrepareClientRates
{
    /**
     * @var CollectClientRates
     */
    private $collectClientRates;

    /**
     * @param CollectClientRates $collectClientRates
     */
    public function __construct(
        CollectClientRates $collectClientRates
    )
    {
        $this->collectClientRates = $collectClientRates;
    }

    /**
     * @return string
     */
    public function prepare()
    {
        $file = sprintf('%s/precios.pdf', sys_get_temp_dir());

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml('<strong>Listado de precios</strong>');

        $html = '<table cellpadding="10">';
        $html .= '<tr><th>País</th><th>Código</th><th>Tipo</th><th>Precio</th></th>';
        $rates = $this->collectClientRates->collect(true);
        foreach ($rates as $rate) {
            $html .= sprintf(
                '<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
                $rate->getCountry(),
                $rate->getCode(),
                $rate->getType(),
                $rate->getSale()
            );
        }
        $html .= '</table>';

        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream($file);

        return $file;
    }
}

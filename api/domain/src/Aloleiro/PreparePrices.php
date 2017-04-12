<?php

namespace Muchacuba\Aloleiro;

use Dompdf\Dompdf;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PreparePrices
{
    /**
     * @var CollectPrices
     */
    private $collectPrices;

    /**
     * @param CollectPrices $collectPrices
     */
    public function __construct(
        CollectPrices $collectPrices
    )
    {
        $this->collectPrices = $collectPrices;
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
        $html .= '<tr><th>País</th><th>Prefijo</th><th>Código</th><th>Tipo</th><th>Valor</th></th>';
        $prices = $this->collectPrices->collect(true);
        foreach ($prices as $price) {
            $html .= sprintf(
                '<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
                $price->getCountry(),
                $price->getPrefix(),
                $price->getCode(),
                $price->getType(),
                $price->getValue()
            );
        }
        $html .= '</table>';

        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream($file);

        return $file;
    }
}

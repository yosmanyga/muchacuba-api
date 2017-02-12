<?php

namespace Muchacuba\Mule;

/**
 * @di\service({deductible: true})
 */
class CollectDestinations
{
    /**
     * @var string[]
     */
    private $destinations;

    public function __construct()
    {
        $this->destinations = [
            'hab' => 'Habana',
            'pri' => 'Pinar del Río',
            'ijv' => 'Isla de la Juventud',
            'art' => 'Artemisa',
            'may' => 'Mayabeque',
            'mtz' => 'Matanzas',
            'cfg' => 'Cienfuegos',
            'vcl' => 'Villa Clara',
            'ssp' => 'Sancti Spíritus',
            'cav' => 'Ciego de Ávila',
            'cmg' => 'Camagüey',
            'ltu' => 'Las Tunas',
            'hol' => 'Holguín',
            'gra' => 'Granma',
            'scu' => 'Santiago de Cuba',
            'gtm' => 'Guantánamo'
        ];
    }

    /**
     * @return array
     */
    public function collect()
    {
        return $this->destinations;
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function pick($key)
    {
        if (!isset($this->destinations[$key])) {
            throw new \InvalidArgumentException($key);
        }

        return $this->destinations[$key];
    }
}

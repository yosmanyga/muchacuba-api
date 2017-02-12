<?php

namespace Muchacuba\Mule;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class SearchOffers
{
    /**
     * @var InitOffer
     */
    private $initOffer;

    /**
     * @param InitOffer $initOffer
     */
    public function __construct(InitOffer $initOffer)
    {
        $this->initOffer = $initOffer;
    }

    /**
     * @param array       $coordinates
     * @param int|null    $radius      Unit in km
     * @param string|null $destination
     * @param int|null    $from
     * @param int|null    $to
     *
     * @return Offer[]
     */
    public function search($coordinates, $radius = null, $destination = null, $from = null, $to = null)
    {
        $radius = ($radius ?: 1000) * 1000;

        // Always search from current date
        if (is_null($from)) {
            $from = time();
        }

        $args = [
            'aroundLatLng' => sprintf('%s, %s', $coordinates['lat'], $coordinates['lng']),
            'aroundRadius' => $radius,
            'facets' => '*',
        ];

        $filters = $this->createFilter($destination, $from, $to);
        if (!empty($filters)) {
            $args['filters'] = $filters;
        }

        $index = $this->initOffer->init();
        $index
            ->setSettings([
                'attributesForFaceting' => ['destinations'],
            ]);

        $res = $index->search('', $args);

        if (!isset($res['hits'])) {
            return [];
        }

        $offers = [];
        foreach ($res['hits'] as $hit) {
            $offers[] = new Offer(
                $hit['objectID'],
                $hit['name'],
                $hit['contact'],
                $hit['address'],
                $hit['_geoloc'],
                $hit['destinations'],
                $hit['description'],
                $hit['trips']
            );
        }

        return $offers;
    }

    /**
     * @param string|null $destination
     * @param int|null    $from
     * @param int|null    $to
     *
     * @return string
     */
    private function createFilter($destination, $from, $to)
    {
        $filters = '';

        if (!is_null($destination)) {
            $filters = sprintf('destinations: "%s"', $destination);
        }

        if (!is_null($from)) {
            if (!empty($filters)) {
                $filters .= ' AND ';
            }

            $filters .= sprintf('nextTrip >= %s', $from);
        }

        if (!is_null($to)) {
            if (!empty($filters)) {
                $filters .= ' AND ';
            }

            $filters .= sprintf('nextTrip <= %s', $to);
        }

        return $filters;
    }
}

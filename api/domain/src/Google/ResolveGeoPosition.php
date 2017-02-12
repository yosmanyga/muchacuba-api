<?php

namespace Muchacuba\Google;

use Geocoder\Provider\GoogleMaps;
use Ivory\HttpAdapter\CurlHttpAdapter;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ResolveGeoPosition
{
    /**
     * @var string
     */
    private $api;

    /**
     * @var GoogleMaps
     */
    private $geoCoder;

    /**
     * @param string $api
     *
     * @di\arguments({
     *    api: "%google_server_api%"
     * })
     */
    public function __construct($api) {
        $this->api = $api;

        $this->geoCoder = new GoogleMaps(
            new CurlHttpAdapter(),
            null,
            null,
            true,
            $this->api
        );
    }

    public function resolve($address)
    {
        return $this->geoCoder->geocode($address);
    }

    public function reverse($lat, $lng)
    {
        return $this->geoCoder->reverse($lat, $lng);
    }
}

<?php

namespace Muchacuba\Mule;

use Faker\Factory;
use Faker\Generator;
use Geocoder\Model\Address;
use Geocoder\Model\AddressCollection;
use Muchacuba\Google\ResolveGeoPosition;
use Muchacuba\Mule\Offer\PurgeStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PopulateOffers
{
    /**
     * @var CollectDestinations
     */
    private $collectDestinations;

    /**
     * @var PurgeStorage
     */
    private $purgeStorage;

    /**
     * @var ManageOffer
     */
    private $manageOffer;

    /**
     * @var ResolveGeoPosition
     */
    private $resolveGeoPosition;

    /**
     * @var Generator
     */
    private $faker;

    /**
     * @param CollectDestinations $collectDestinations
     * @param PurgeStorage        $purgeStorage
     * @param ManageOffer         $manageOffer
     * @param ResolveGeoPosition  $resolveGeoPosition
     */
    public function __construct(
        CollectDestinations $collectDestinations,
        PurgeStorage $purgeStorage,
        ManageOffer $manageOffer,
        ResolveGeoPosition $resolveGeoPosition
    )
    {
        $this->collectDestinations = $collectDestinations;
        $this->purgeStorage = $purgeStorage;
        $this->manageOffer = $manageOffer;
        $this->resolveGeoPosition = $resolveGeoPosition;
        $this->faker = $this->faker = Factory::create('es_ES');
    }

    /**
     */
    public function populateInMiami()
    {
        $this->purgeStorage->purge();

        $this->populateAroundAddress(
            'Miami International Airport (MIA), NW 42nd Ave, Miami, FL',
            0,
            800,
            20
        );
    }

    /**
     * @param string $aroundAddress
     * @param int    $from Distance in km
     * @param int    $to Distance in km
     * @param int    $amount
     *
     * @throws \Exception
     */
    public function populateAroundAddress($aroundAddress, $from, $to, $amount)
    {
        try {
            $addresses = $this->resolveGeoPosition->resolve($aroundAddress);
        } catch (\Exception $e) {
            throw $e;
        }

        for ($i = 1; $i <= $amount; $i++) {
            $point = $this->generatePoint(
                $addresses->first()->getCoordinates()->getLatitude(),
                $addresses->first()->getCoordinates()->getLongitude(),
                $from,
                $to
            );

            $this->manageOffer->insert(
                null,
                $this->faker->name,
                sprintf("Tel: %s\r\nEmail: %s", $this->faker->phoneNumber, $this->faker->email),
                $point['address'],
                $point['coordinates'],
                $this->generateDestinations(),
                $this->faker->paragraph(),
                $this->generateTrips()
            );
        }
    }

    /**
     * @return array
     */
    private function generateDestinations()
    {
        $places = $this->collectDestinations->collect();
        $amount = rand(1, count($places));
        $destinations = array_slice(array_keys($places), 0, $amount);

        return $destinations;
    }

    /**
     * @param float $lat
     * @param float $lng
     * @param int   $from Unit in km
     * @param int   $to   Unit in km
     *
     * @return array
     */
    private function generatePoint($lat, $lng, $from, $to)
    {
        $coordinates = null;
        $address = null;

        while ($address === null) {
            // From http://gis.stackexchange.com/a/2980

            $c = rand($from, $to) * 1000;
            $a = rand(1, $c - 1);
            $b = sqrt(pow($c, 2) - pow($a, 2));

            // Offsets
            $dn = $a;
            $de = $b;

            // Earth’s radius, sphere
            $r = 6378137;

            // Coordinate offsets in radians
            $dLat = $dn / $r;
            $dLng = $de / ($r * cos(M_PI * $lat / 180));

            $n = [1, -1];

            $dLat = $dLat * $n[rand(0, 1)];
            $dLng = $dLng * $n[rand(0, 1)];

            $coordinates = [
                'lat' => $lat + $dLat * 180 / M_PI,
                'lng' => $lng + $dLng * 180 / M_PI
            ];

            /** @var AddressCollection $addresses */
            try {
                $addresses = $this->resolveGeoPosition->reverse($coordinates['lat'], $coordinates['lng']);
            } catch (\Exception $e) {
                continue;
            }

            /** @var Address $address */
            foreach ($addresses as $address) {
                if (
                    $address->getStreetName() === null
                    || $address->getStreetName() == 'Unnamed Road'
                    || $address->getLocality() === null
                    || $address->getPostalCode() === null
                ) {
                    $address = null;

                    continue;
                }

                $address = implode(', ', [
                    $address->getStreetName(),
                    $address->getLocality(),
                    $address->getPostalCode()
                ]);

                break;
            }
        }

        return [
            'address' => $address,
            'coordinates' => $coordinates
        ];
    }

    /**
     * @return array
     */
    private function generateTrips() {
        $amount = rand(1, 5);
        
        $trips = [];
        for ($i = 1; $i <= $amount; $i++) {
            $trips[] = (int) $this->faker->dateTimeBetween('now', '+365 days')->getTimestamp();
        }

        return $trips;
    }
}

<?php

namespace Muchacuba\Mule;

use Muchacuba\Mule\Offer\ConnectToStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PickOffer
{
    /**
     * @var ConnectToStorage
     */
    private $connectToStorage;

    /**
     * @param ConnectToStorage $connectToStorage
     */
    public function __construct(
        ConnectToStorage $connectToStorage
    ) {
        $this->connectToStorage = $connectToStorage;
    }

    /**
     * @param string $id
     *
     * @throws NonExistentOfferException
     *
     * @return Offer
     */
    public function pick($id)
    {
        $criteria = [
            '_id' => $id,
        ];

        /** @var Offer $offer */
        $offer = $this->connectToStorage->connect()
            ->findOne($criteria);

        if (is_null($offer)) {
            throw new NonExistentOfferException();
        }

        return $offer;
    }
}

<?php

namespace Muchacuba\Mule\Me;

use Muchacuba\Mule\NonExistentOfferException;
use Muchacuba\Mule\NonExistentProfileException;
use Muchacuba\Mule\Offer\InvalidDataException;
use Muchacuba\Mule\Profile;
use Muchacuba\Mule\Profile\ManageStorage;
use Muchacuba\Mule\ManageOffer as BaseManageOffer;
use Muchacuba\Mule\PickOffer as BasePickOffer;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ManageOffer
{
    /**
     * @var BasePickOffer
     */
    private $pickOffer;

    /**
     * @var BaseManageOffer
     */
    private $manageOffer;

    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param BasePickOffer     $pickOffer
     * @param BaseManageOffer   $manageOffer
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        BasePickOffer $pickOffer,
        BaseManageOffer $manageOffer,
        ManageStorage $manageStorage
    )
    {
        $this->pickOffer = $pickOffer;
        $this->manageOffer = $manageOffer;
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param string      $uniqueness
     * @param string|null $id
     * @param string      $name
     * @param string      $contact
     * @param string      $address
     * @param array       $geo          Array with keys "lat" and "lng"
     * @param string[]    $destinations
     * @param string      $description
     * @param int[]       $trips
     *
     * @throws InvalidDataException
     * @throws \Exception
     * @throws NonExistentOfferException
     */
    public function create(
        $uniqueness,
        $id,
        $name,
        $contact,
        $address,
        $geo,
        $destinations,
        $description,
        $trips
    )
    {
        try {
            $offer = $this->manageOffer->insert(
                $id,
                $name,
                $contact,
                $address,
                $geo,
                $destinations,
                $description,
                $trips
            );
        } catch (InvalidDataException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }

        $this->manageStorage->connect()->insertOne(new Profile(
            $uniqueness,
            $offer
        ));
    }

    /**
     * @param string      $uniqueness
     * @param string|null $id
     * @param string      $name
     * @param string      $contact
     * @param string      $address
     * @param array       $geo          Array with keys "lat" and "lng"
     * @param string[]    $destinations
     * @param string      $description
     * @param int[]       $trips
     *
     * @throws NonExistentProfileException
     * @throws InvalidDataException
     * @throws NonExistentOfferException
     * @throws \Exception
     */
    public function update(
        $uniqueness,
        $id,
        $name,
        $contact,
        $address,
        $geo,
        $destinations,
        $description,
        $trips
    )
    {
        /** @var Profile $profile */
        $profile = $this->manageStorage->connect()
            ->findOne([
                '_id' => $uniqueness,
                'offer' => $id
            ]);

        if (is_null($profile)) {
            throw new NonExistentProfileException();
        }

        try {
            $this->manageOffer->update(
                $id,
                $name,
                $contact,
                $address,
                $geo,
                $destinations,
                $description,
                $trips
            );
        } catch (InvalidDataException $e) {
            throw $e;
        } catch (NonExistentOfferException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

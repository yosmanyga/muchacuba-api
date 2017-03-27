<?php

namespace Muchacuba\Mule;

use Muchacuba\Mule\Offer\ConnectToStorage;
use Muchacuba\Mule\Offer\InvalidDataException;
use Yosmanyga\Validation\Validator\ArrayValidator;
use Yosmanyga\Validation\Validator\ExceptionValidator;
use Yosmanyga\Validation\Validator\ScalarValidator;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ManageOffer
{
    /**
     * @var CollectDestinations
     */
    private $collectDestinations;

    /**
     * @var ConnectToStorage
     */
    private $connectToStorage;

    /**
     * @var InitOffer
     */
    private $initOffer;

    /**
     * @param CollectDestinations $collectDestinations
     * @param ConnectToStorage    $connectToStorage
     * @param InitOffer           $initOffer
     */
    public function __construct(
        CollectDestinations $collectDestinations,
        ConnectToStorage $connectToStorage,
        InitOffer $initOffer
    ) {
        $this->collectDestinations = $collectDestinations;
        $this->connectToStorage = $connectToStorage;
        $this->initOffer = $initOffer;
    }

    /**
     * @param string|null $id
     * @param string      $name
     * @param string      $contact
     * @param string      $address
     * @param array       $coordinates  Array with keys "lat" and "lng"
     * @param string[]    $destinations
     * @param string      $description
     * @param int[]       $trips
     *
     * @return string
     *
     * @throws InvalidDataException
     * @throws \Exception
     */
    public function insert(
        $id,
        $name,
        $contact,
        $address,
        $coordinates,
        $destinations,
        $description,
        $trips
    ) {
        try {
            $this->validateInsert(
                $id,
                $name,
                $contact,
                $address,
                $coordinates,
                $destinations,
                $description,
                $trips
            );
        } catch (InvalidDataException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }

        $id = $id ?: uniqid();

        $this->connectToStorage->connect()->insertOne(new Offer(
            $id,
            $name,
            $contact,
            $address,
            $coordinates,
            $destinations,
            $description,
            $trips
        ));

        $this->indexOnAlgolia(
            $id,
            $name,
            $contact,
            $address,
            $coordinates,
            $destinations,
            $description,
            $trips
        );

        return $id;
    }

    /**
     * @param string   $id
     * @param string   $name
     * @param string   $contact
     * @param string   $address
     * @param array    $coordinates
     * @param string[] $destinations
     * @param string   $description
     * @param int[]    $trips
     *
     * @throws InvalidDataException
     * @throws NonExistentOfferException
     * @throws \Exception
     *
     * @return string
     */
    public function update(
        $id,
        $name,
        $contact,
        $address,
        $coordinates,
        $destinations,
        $description,
        $trips
    )
    {
        try {
            $this->validateUpdate(
                $name,
                $contact,
                $address,
                $coordinates,
                $destinations,
                $description,
                $trips
            );
        } catch (InvalidDataException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }

        $result = $this->connectToStorage->connect()->updateOne(
            ['_id' => $id],
            ['$set' => [
                'name' => $name,
                'contact' => $contact,
                'address' => $address,
                'coordinates' => $coordinates,
                'destinations' => $destinations,
                'description' => $description,
                'trips' => $trips,
            ]]);

        if (!$result) {
            throw new NonExistentOfferException();
        }

        $this->indexOnAlgolia(
            $id,
            $name,
            $contact,
            $address,
            $coordinates,
            $destinations,
            $description,
            $trips
        );

        return $id;
    }

    /**
     * @param string $id
     *
     * @throws NonExistentOfferException
     */
    public function delete($id)
    {
        $result = $this->connectToStorage->connect()->deleteOne([
            '_id' => $id,
        ]);

        if ($result->getDeletedCount() === 0) {
            throw new NonExistentOfferException($id);
        }

        $this->deleteOnAlgolia($id);
    }

    /**
     * @param string   $name
     * @param string   $contact
     * @param string   $address
     * @param array    $coordinates
     * @param string[] $destinations
     * @param string   $description
     * @param int[]    $trips
     *
     * @throws InvalidDataException
     * @throws \Exception
     */
    private function validateCommon(
        $name,
        $contact,
        $address,
        $coordinates,
        $destinations,
        $description,
        $trips
    )
    {
        /* Validate name */

        try {
            (new ExceptionValidator(new ScalarValidator([
                'type' => 'string',
                'neq' => ''
            ])))->validate($name);
        } catch (\RuntimeException $e) {
            throw new InvalidDataException(InvalidDataException::FIELD_NAME, InvalidDataException::TYPE_EMPTY);
        }

        /* Validate contact */

        try {
            (new ExceptionValidator(new ScalarValidator([
                'type' => 'string',
                'neq' => ''
            ])))->validate($contact);
        } catch (\RuntimeException $e) {
            throw new InvalidDataException(InvalidDataException::FIELD_CONTACT, InvalidDataException::TYPE_EMPTY);
        }

        /* Validate address */

        try {
            (new ExceptionValidator(new ScalarValidator([
                'type' => 'string',
                'neq' => ''
            ])))->validate($address);
        } catch (\RuntimeException $e) {
            throw new InvalidDataException(InvalidDataException::FIELD_ADDRESS, InvalidDataException::TYPE_EMPTY);
        }

        /* Validate coordinates */

        try {
            (new ExceptionValidator(new ScalarValidator([
                'type' => 'array',
            ])))->validate($coordinates);
        } catch (\RuntimeException $e) {
            throw new InvalidDataException(InvalidDataException::FIELD_COORDINATES, InvalidDataException::TYPE_EMPTY);
        }

        try {
            (new ExceptionValidator(new ArrayValidator([
                'requiredKeys' => ['lat', 'lng']
            ])))->validate($coordinates);
        } catch (\RuntimeException $e) {
            throw new \Exception(null, null, $e);
        }

        /* Validate destinations */

        try {
            (new ExceptionValidator(new ScalarValidator([
                'type' => 'array',
                'neq' => []
            ])))->validate($destinations);
        } catch (\RuntimeException $e) {
            throw new InvalidDataException(InvalidDataException::FIELD_DESTINATIONS, InvalidDataException::TYPE_EMPTY);
        }

        $validator = new ExceptionValidator(new ScalarValidator([
            'in' => array_keys($this->collectDestinations->collect()),
        ]));

        try {
            foreach ($destinations as $destination) {
                $validator->validate($destination);
            }
        } catch (\RuntimeException $e) {
            throw new \Exception(null, null, $e);
        }

        /* Validate description */

        try {
            (new ExceptionValidator(new ScalarValidator([
                'type' => 'string',
                'neq' => ''
            ])))->validate($description);
        } catch (\RuntimeException $e) {
            throw new InvalidDataException(InvalidDataException::FIELD_DESCRIPTION, InvalidDataException::TYPE_EMPTY);
        }

        /* Validate trips */

        try {
            (new ExceptionValidator(new ScalarValidator([
                'type' => 'array',
                'neq' => []
            ])))->validate($trips);
        } catch (\RuntimeException $e) {
            throw new InvalidDataException(InvalidDataException::FIELD_TRIPS, InvalidDataException::TYPE_EMPTY);
        }

        $validator = new ExceptionValidator(new ScalarValidator([
            'type' => 'integer',
        ]));

        try {
            foreach ($trips as $trip) {
                $validator->validate($trip);
            }
        } catch (\RuntimeException $e) {
            throw new \Exception(null, null, $e);
        }
    }

    /**
     * @param string   $id
     * @param string   $name
     * @param string   $contact
     * @param string   $address
     * @param array    $coordinates
     * @param string[] $destinations
     * @param string   $description
     * @param int[]    $trips
     *
     * @throws InvalidDataException
     * @throws \Exception
     */
    private function validateInsert(
        $id,
        $name,
        $contact,
        $address,
        $coordinates,
        $destinations,
        $description,
        $trips
    )
    {
        /* Validate id */

        $validator = new ExceptionValidator(new ScalarValidator([
            'type' => 'string',
            'allowNull' => true,
        ]));

        try {
            $validator->validate($id);
        } catch (\RuntimeException $e) {
            throw new \Exception(null, null, $e);
        }

        try {
            $this->validateCommon(
                $name,
                $contact,
                $address,
                $coordinates,
                $destinations,
                $description,
                $trips
            );
        } catch (InvalidDataException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param string   $name
     * @param string   $contact
     * @param string   $address
     * @param array    $coordinates
     * @param string[] $destinations
     * @param string   $description
     * @param int[]    $trips
     *
     * @throws InvalidDataException
     * @throws \Exception
     */
    private function validateUpdate(
        $name,
        $contact,
        $address,
        $coordinates,
        $destinations,
        $description,
        $trips
    )
    {
        try {
            $this->validateCommon(
                $name,
                $contact,
                $address,
                $coordinates,
                $destinations,
                $description,
                $trips
            );
        } catch (InvalidDataException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Adds an object on algolia with given data.
     *
     * @param string $uniqueness
     * @param string $name
     * @param string $contact
     * @param string $address
     * @param array  $coordinates
     * @param string $destinations
     * @param string $description
     * @param int[]  $trips
     */
    private function indexOnAlgolia(
        $uniqueness,
        $name,
        $contact,
        $address,
        $coordinates,
        $destinations,
        $description,
        $trips
    )
    {
        $index = $this->initOffer->init();
        $index->setSettings([
            'customRanking' => ['asc(nextTrip)'],
            'searchableAttributes' => ['name', 'contact', 'address', 'description']
        ]);

        $nextTrip = min($trips);

        $res = $index->addObject([
            'objectID' => $uniqueness,
            'name' => $name,
            'contact' => $contact,
            'address' => $address,
            '_geoloc' => $coordinates,
            'destinations' => $destinations,
            'description' => $description,
            'trips' => $trips,
            'nextTrip' => $nextTrip,
        ]);
        $index->waitTask($res['taskID']);
    }

    /**
     * Deletes object from algolia with given id.
     *
     * @param string $id
     */
    private function deleteOnAlgolia($id)
    {
        $this->initOffer->init()->deleteObject($id);
    }
}

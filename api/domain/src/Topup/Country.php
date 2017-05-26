<?php

namespace Muchacuba\Topup;

use MongoDB\BSON\Persistable;
use Muchacuba\Topup\Country\Dialing;

class Country implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $iso;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Dialing[]
     */
    private $dialings;

    /**
     * @param string    $iso
     * @param string    $name
     * @param Dialing[] $dialings
     */
    public function __construct(
        $iso,
        $name,
        array $dialings
    ) {
        $this->iso = $iso;
        $this->name = $name;
        $this->dialings = $dialings;
    }

    /**
     * @return string
     */
    public function getIso()
    {
        return $this->iso;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Dialing[]
     */
    public function getDialings()
    {
        return $this->dialings;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->iso,
            'name' => $this->name,
            'dialings' => array_map(function(Dialing $dialing) {
                return new Dialing(
                    $dialing->getPrefix(),
                    $dialing->getMinLength(),
                    $dialing->getMaxLength()
                );
            }, $this->dialings)
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->iso = $data['_id'];
        $this->name = $data['name'];
        $this->dialings = array_map(function($dialing) {
            return new Dialing(
                $dialing['prefix'],
                $dialing['minLength'],
                $dialing['maxLength']
            );
        }, $data['dialings']);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'iso' => $this->iso,
            'name' => $this->name,
            'dialings' => $this->dialings
        ];
    }
}

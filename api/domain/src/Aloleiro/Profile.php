<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\Persistable;

class Profile implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $uniqueness;

    /**
     * @var int
     */
    private $profitFactor;

    /**
     * @var string[]
     */
    private $phones;

    /**
     * @param string   $uniqueness
     * @param int      $profitFactor
     * @param string[] $phones
     */
    public function __construct(
        $uniqueness,
        $profitFactor,
        array $phones = []
    ) {
        $this->uniqueness = $uniqueness;
        $this->profitFactor = $profitFactor;
        $this->phones = $phones;
    }

    /**
     * @return string
     */
    public function getUniqueness()
    {
        return $this->uniqueness;
    }

    /**
     * @return int
     */
    public function getProfitFactor(): int
    {
        return $this->profitFactor;
    }

    /**
     * @return string
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->uniqueness,
            'profitFactor' => $this->profitFactor,
            'phones' => $this->phones
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->uniqueness = $data['_id'];
        $this->profitFactor = $data['profitFactor'];
        $this->phones = $data['phones'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'uniqueness' => $this->uniqueness,
            'profitFactor' => $this->profitFactor,
            'phones' => $this->phones,
        ];
    }
}

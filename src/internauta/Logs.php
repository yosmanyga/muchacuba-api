<?php

namespace Muchacuba\Internauta;

use MongoDB\Driver\Cursor;

class Logs implements \IteratorAggregate, \JsonSerializable
{
    /**
     * @var Log[]
     */
    private $items;

    /**
     * @param Cursor $items
     */
    public function __construct(Cursor $items)
    {
        $this->items = $items;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->items;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = $item->jsonSerialize();
        }

        return $items;
    }
}

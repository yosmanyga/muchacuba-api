<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Event\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectEvents
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        ManageStorage $manageStorage
    )
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @return Event[]
     */
    public function collect()
    {
        $events = $this->manageStorage->connect()->find();

        return iterator_to_array($events);
    }
}

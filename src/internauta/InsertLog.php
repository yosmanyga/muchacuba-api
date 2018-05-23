<?php

namespace Muchacuba\Internauta;

use Muchacuba\Internauta\Log\ManageStorage;

/**
 * @di\service({
 *     private: true
 * })
 */
class InsertLog
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage $manageStorage
     */
    public function __construct(ManageStorage $manageStorage)
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param string      $type
     * @param mixed       $payload
     * @param int         $date
     * @param string|null $id
     *
     * @return string
     */
    public function insert($type, $payload, $date, $id = null)
    {
        $id = $id ?: uniqid();

        $this->manageStorage->connect()->insertOne(new Log(
            $id ?: uniqid(),
            $type,
            $payload,
            $date
        ));

        return $id;
    }
}
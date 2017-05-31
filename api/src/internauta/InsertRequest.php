<?php

namespace Muchacuba\Internauta;

use Muchacuba\Internauta\Request\ManageStorage;

/**
 * @di\service({
 *     private: true
 * })
 */
class InsertRequest
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
    ) {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param string      $from
     * @param string      $to
     * @param string      $subject
     * @param string      $body
     * @param string|null $id
     *
     * @return string
     */
    public function insert(
        $from,
        $to,
        $subject,
        $body,
        $id = null
    ) {
        $id = $id ?: uniqid();

        $this->manageStorage->connect()->insertOne(new Request(
            $id,
            $from,
            $to,
            $subject,
            $body
        ));

        return $id;
    }
}
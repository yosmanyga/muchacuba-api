<?php

namespace Muchacuba\Internauta\Advertising;

use Muchacuba\Internauta\Advertising\Email\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class InsertEmail
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
     * @param string      $subject
     * @param string      $body
     * @param string|null $id
     *
     * @return string
     */
    public function insert(
        $subject,
        $body,
        $id = null
    ) {
        $id = $id ?: uniqid();

        $this->manageStorage->connect()->insertOne(new Email(
            $id,
            $subject,
            $body
        ));

        return $id;
    }
}
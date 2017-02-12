<?php

namespace Muchacuba;

use Muchacuba\Feedback\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class InsertFeedback
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
     * @param string|null $id
     * @param string      $text
     *
     * @return string
     */
    public function insert(
        $id,
        $text
    ) {
        $id = $id ?: uniqid();

        $this->manageStorage->connect()->insertOne(new Feedback(
            $id,
            $text
        ));

        return $id;
    }
}

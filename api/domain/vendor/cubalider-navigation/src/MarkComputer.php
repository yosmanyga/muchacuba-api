<?php

namespace Cubalider\Navigation;

use Cubalider\Navigation\Computer\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class MarkComputer
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
     * @param string $uniqueness
     */
    public function markWorking($uniqueness)
    {
        $this->mark($uniqueness, Computer::STATUS_WORKING);
    }

    /**
     * @param string $id
     */
    public function markNotWorking($id)
    {
        $this->mark($id, Computer::STATUS_NOT_WORKING);
    }

    /**
     * @param string $id
     * @param int    $status
     */
    private function mark($id, $status)
    {
        $this->manageStorage->connect()->updateOne(
            ['_id' => $id],
            [
                '$set' => [
                    'status' => $status
                ]
            ]
        );
    }
}
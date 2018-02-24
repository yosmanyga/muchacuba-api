<?php

namespace Yosmy\Navigation;

use Yosmy\Navigation\Computer\ManageStorage;

/**
 * @author Yosmany Garcia <yosmanyga@gmail.com>
 *
 * @di\service({
 *     private: true
 * })
 */
class PickComputer
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage  $manageStorage
     */
    public function __construct(
        ManageStorage $manageStorage
    )
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param int|null $amount
     * 
     * @return Computer[]
     */
    public function pickUnknown($amount = 1)
    {
        return $this->pick(Computer::STATUS_UNKNOWN, $amount);
    }

    /**
     * @param int|null $amount
     *
     * @return Computer[]
     */
    public function pickWorking($amount)
    {
        return $this->pick(Computer::STATUS_WORKING, $amount);
    }

    /**
     * @param int $status
     * @param int $amount
     *
     * @return Computer[]
     */
    private function pick($status, $amount)
    {
        $computers = $this->manageStorage->connect()
            ->aggregate([
                ['$match' => ['status' => $status]],
                ['$sample' => ['size' => $amount]]
            ]);

        return iterator_to_array($computers);
    }
}

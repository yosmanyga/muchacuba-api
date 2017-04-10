<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ProcessICEEvent
{
    /**
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @param ManageCallStorage $manageCallStorage
     */
    public function __construct(ManageCallStorage $manageCallStorage)
    {
        $this->manageCallStorage = $manageCallStorage;
    }

    /**
     * @param string $from
     * @param string $to
     *
     * @return string
     */
    public function process($from, $to)
    {
        /** @var Call $call */
        $call = $this->manageCallStorage->connect()->findOne([
            'from' => $from
        ]);

        if (is_null($call)) {
            return $this->prepareHangup();
        }

        return $this->prepareConnect($from, $to);
    }

    /**
     * @return string
     */
    private function prepareHangup()
    {
        return <<<EOF
{
    "name" : "Hangup"
}
EOF;
    }

    /**
     * @param string $from
     * @param string $to
     *
     * @return string
     */
    private function prepareConnect($from, $to)
    {
        return <<<EOF
{
    "name" : "ConnectPSTN",
    "number" : "{$to}",
    "maxDuration" : 3600,
    "cli" : "{$from}"
}
EOF;
    }
}
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
     *
     * @return string
     */
    public function process($from)
    {
        /** @var Call $call */
        $call = $this->manageCallStorage->connect()->findOne([
            'from' => $from
        ]);

        if (is_null($call)) {
            return $this->prepareHangup();
        }

        return $this->prepareConnect($from, $call->getTo());
    }

    /**
     * @return string
     */
    private function prepareHangup()
    {
        return <<<EOF
{
    "Action": {
        "name" : "Hangup"
    }
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
    "Action": {
        "name" : "ConnectPSTN",
        "number" : "{$to}",
        "maxDuration" : 3600,
        "cli" : "{$from}"
    }
}
EOF;
    }
}
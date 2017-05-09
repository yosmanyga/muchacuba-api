<?php

namespace Muchacuba\Aloleiro\Test;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Muchacuba\Aloleiro\CollectCalls;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{name: 'muchacuba.aloleiro.check_state', key: 'call'}]
 * })
 */
class CheckCallState implements CheckState
{
    /**
     * @var bool
     */
    private $ignore;

    /**
     * @var array
     */
    private $state;

    /**
     * @var CollectCalls
     */
    private $collectCalls;

    /**
     * @param CollectCalls $collectCalls
     */
    public function __construct(CollectCalls $collectCalls)
    {
        $this->collectCalls = $collectCalls;
    }

    /**
     * {@inheritdoc}
     */
    public function shoot()
    {
        $this->ignore = false;

        $this->state = $this->collectCalls->collect();
    }

    /**
     * {@inheritdoc}
     */
    public function compare()
    {
        if ($this->ignore) {
            return;
        }

        $preparedCalls = $this->collectCalls->collect();

        $matcher = (new SimpleFactory())->createMatcher();

        if (!$matcher->match(
            json_decode(json_encode($preparedCalls), true),
            json_decode(json_encode($this->state), true)
        )) {
            throw new \Exception($matcher->getError());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function ignore()
    {
        $this->ignore = true;
    }
}

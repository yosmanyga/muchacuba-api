<?php

namespace Muchacuba\Aloleiro\Test;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Muchacuba\Aloleiro\CollectBusinesses;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{name: 'muchacuba.aloleiro.check_state', key: 'business'}]
 * })
 */
class CheckBusinessState implements CheckState
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
     * @var CollectBusinesses
     */
    private $collectBusinesses;

    /**
     * @param CollectBusinesses $collectBusinesses
     */
    public function __construct(CollectBusinesses $collectBusinesses)
    {
        $this->collectBusinesses = $collectBusinesses;
    }

    /**
     * {@inheritdoc}
     */
    public function shoot()
    {
        $this->ignore = false;

        $this->state = $this->collectBusinesses->collect();
    }

    /**
     * {@inheritdoc}
     */
    public function compare()
    {
        if ($this->ignore) {
            return;
        }

        $businesses = $this->collectBusinesses->collect();

        $matcher = (new SimpleFactory())->createMatcher();

        if (!$matcher->match(
            json_decode(json_encode($businesses), true),
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

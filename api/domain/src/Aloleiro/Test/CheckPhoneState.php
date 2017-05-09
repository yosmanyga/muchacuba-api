<?php

namespace Muchacuba\Aloleiro\Test;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Muchacuba\Aloleiro\CollectPhones;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{name: 'muchacuba.aloleiro.check_state', key: 'phone'}]
 * })
 */
class CheckPhoneState implements CheckState
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
     * @var CollectPhones
     */
    private $collectPhones;

    /**
     * @param CollectPhones $collectPhones
     */
    public function __construct(CollectPhones $collectPhones)
    {
        $this->collectPhones = $collectPhones;
    }

    /**
     * {@inheritdoc}
     */
    public function shoot()
    {
        $this->ignore = false;

        $this->state = $this->collectPhones->collect();
    }

    /**
     * {@inheritdoc}
     */
    public function compare()
    {
        if ($this->ignore) {
            return;
        }

        $phones = $this->collectPhones->collect();

        $matcher = (new SimpleFactory())->createMatcher();

        if (!$matcher->match(
            json_decode(json_encode($phones), true),
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

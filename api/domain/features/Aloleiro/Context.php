<?php

namespace Muchacuba\Aloleiro;

use Behat\Behat\Context\Context as BaseContext;
use Muchacuba\Aloleiro\Test\CheckAllStates;
use Symsonte\Behat\ContainerAwareContext;
use Symsonte\Service\Container;

class Context implements BaseContext, ContainerAwareContext
{
    /**
     * @var PurgeStorages
     */
    private $purgeStorages;

    /**
     * @var CheckAllStates
     */
    private $checkAllStates;

    /**
     * {@inheritdoc}
     */
    public function setContainer(Container $container)
    {
        $this->purgeStorages = $container->get(PurgeStorages::class);
        $this->checkAllStates = $container->get(CheckAllStates::class);
    }

    /**
     * @BeforeScenario
     */
    public function purgeStorages()
    {
        $this->purgeStorages->purge();
    }

    /**
     * @BeforeScenario
     */
    public function shootStates()
    {
        $this->checkAllStates->shoot();
    }
}

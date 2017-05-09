<?php

namespace Muchacuba\Aloleiro\Test;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CheckAllStates
{
    /**
     * @var CheckState[]
     */
    private $checkStateServices;

    /**
     * @param CheckState[] $checkStateServices
     *
     * @di\arguments({
     *     checkStateServices: '#muchacuba.aloleiro.check_state'
     * })
     */
    public function __construct(
        array $checkStateServices
    )
    {
        $this->checkStateServices = $checkStateServices;
    }

    /**
     * @param string $key
     */
    public function shoot($key = null)
    {
        if ($key != null) {
            $this->checkStateServices[$key]->shoot();

            return;
        }

        foreach ($this->checkStateServices as $checkStateService) {
            $checkStateService->shoot();
        }
    }

    /**
     */
    public function compare()
    {
        foreach ($this->checkStateServices as $checkStateService) {
            $checkStateService->compare();
        }
    }

    /**
     * @param string $key
     */
    public function ignore($key)
    {
        $this->checkStateServices[$key]->ignore();
    }
}
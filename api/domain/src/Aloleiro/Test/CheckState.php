<?php

namespace Muchacuba\Aloleiro\Test;

interface CheckState
{
    /**
     */
    public function shoot();

    /**
     * @throws \Exception
     */
    public function compare();

    /**
     */
    public function ignore();
}

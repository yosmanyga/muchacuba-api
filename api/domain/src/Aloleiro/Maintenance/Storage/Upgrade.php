<?php

namespace Muchacuba\Aloleiro\Maintenance\Storage;

interface Upgrade
{
    /**
     * @param string|null $last
     *
     * @return bool
     */
    public function upgrade($last);
}
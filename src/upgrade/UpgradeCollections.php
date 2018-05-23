<?php

namespace Muchacuba;

interface UpgradeCollections
{
    /**
     * @param string|null $last
     *
     * @return bool
     */
    public function upgrade($last);
}
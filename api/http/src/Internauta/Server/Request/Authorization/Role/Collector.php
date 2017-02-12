<?php

namespace Muchacuba\Http\Internauta\Server\Request\Authorization\Role;

use Symsonte\Http\Server\Request\Authorization\Role\Collector as BaseCollector;

/**
 * @di\service({
 *     private: true
 * })
 */
class Collector implements BaseCollector
{
    /**
     * {@inheritdoc}
     */
    public function collect($uniqueness)
    {
    }
}

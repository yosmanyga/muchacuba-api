<?php

namespace Cubalider\Navigation;

use Cubalider\Navigation\Computer\ManageStorage;
use MongoDB\Driver\Exception\BulkWriteException;

/**
 * @author Yosmany Garcia <yosmanyga@gmail.com>
 *
 * @di\service({deductible: true})
 */
class CreateComputers
{
    /**
     * @var ResolveProxies
     */
    private $resolveProxies;

    /**
     * @var ResolveAgents
     */
    private $resolveAgents;

    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ResolveProxies $resolveProxies
     * @param ResolveAgents  $resolveAgents
     * @param ManageStorage  $manageStorage
     */
    public function __construct(
        ResolveProxies $resolveProxies,
        ResolveAgents $resolveAgents,
        ManageStorage $manageStorage
    )
    {
        $this->resolveProxies = $resolveProxies;
        $this->resolveAgents = $resolveAgents;
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param int $amount
     *
     * @return int
     */
    public function create($amount = 100)
    {
        $proxies = $this->resolveProxies->resolve($amount);
        $agents = $this->resolveAgents->resolve();

        $c = 0;
        foreach ($proxies as $proxy) {
            try {
                $this->manageStorage->connect()->insertOne(
                    new Computer(
                        uniqid(),
                        $proxy['ip'],
                        $proxy['port'],
                        $proxy['protocol'],
                        $agents[rand(0, count($agents) - 1)]
                    )
                );

                $c++;
            } catch (BulkWriteException $e) {
                if ($e->getCode() == 'E11000') {
                    if (strpos($e->getMessage(), 'ip_1_port_1') !== false) {
                        continue;
                    }
                }

                throw $e;
            }
        }

        return $c;
    }
}

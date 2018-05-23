<?php

namespace Yosmy\Navigation;

use MongoDB\BSON\Persistable;

/**
 * @author Yosmany Garcia <yosmanyga@gmail.com>
 */
class Computer implements Persistable, \JsonSerializable
{
    const STATUS_UNKNOWN = null;
    const STATUS_WORKING = 1;
    const STATUS_NOT_WORKING = 2;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var string
     */
    private $port;

    /**
     * @var string
     */
    private $protocol;

    /**
     * @var string
     */
    private $agent;

    /**
     * @var int
     */
    private $status;

    /**
     * @param string   $id
     * @param string   $ip
     * @param string   $port
     * @param string   $protocol
     * @param string   $agent
     * @param int|null $status
     */
    public function __construct(
        $id,
        $ip,
        $port,
        $protocol,
        $agent,
        $status = self::STATUS_UNKNOWN)
    {
        $this->id = $id;
        $this->ip = $ip;
        $this->port = $port;
        $this->agent = $agent;
        $this->protocol = $protocol;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'ip' => $this->ip,
            'port' => $this->port,
            'protocol' => $this->protocol,
            'agent' => $this->agent,
            'status' => $this->status
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->ip = $data['ip'];
        $this->port = $data['port'];
        $this->protocol = $data['protocol'];
        $this->agent = $data['agent'];
        $this->status = $data['status'];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id'    => $this->id,
            'ip'    => $this->ip,
            'port' => $this->port,
            'protocol' => $this->protocol,
            'agent' => $this->agent,
            'status' => $this->status
        ];
    }
}

<?php

namespace Cubalider\Voip;

interface ListenCompletedEvent
{
    /**
     * @param string $id
     * @param int    $start
     * @param int    $end
     * @param int    $duration
     * @param float  $cost
     */
    public function listen($id, $start, $end, $duration, $cost);
}
<?php

namespace PhpGpio\Sensors;

/**
 * Interface implemented by sensors classes.
 */
interface SensorInterface
{

    /**
     * Read
     *
     * @param array $args
     * @return float
     */
    public function read($args = []): float;
}

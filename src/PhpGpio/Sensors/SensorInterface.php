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
     * @access public
     * @param  array $args
     * @return double
     */
    public function read($args = array());

    /**
     * Write
     *
     * @access public
     * @param  array $args
     * @return $this
     */
    public function write($args = array());

}

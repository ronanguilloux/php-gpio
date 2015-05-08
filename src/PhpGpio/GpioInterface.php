<?php

namespace PhpGpio;

/**
 * Gpio interface
 *
 * @author Vaidas Lažauskas <vaidas@notrix.lt>
 */
interface GpioInterface
{
    const DIRECTION_IN  = 'in';
    const DIRECTION_OUT = 'out';

    const IO_VALUE_ON   = 1;
    const IO_VALUE_OFF  = 0;

    const PATH_GPIO     = '/sys/class/gpio/gpio';
    const PATH_EXPORT   = '/sys/class/gpio/export';
    const PATH_UNEXPORT = '/sys/class/gpio/unexport';

    /**
     * getHackablePins : the pins you can hack with.
     *
     * @access public
     * @link   http://elinux.org/RPi_Low-level_peripherals
     * @return array
     */
    public function getHackablePins();

    /**
     * Setup pin, takes pin number and direction (in or out)
     *
     * @access public
     * @param  int    $pinNo
     * @param  string $direction
     *
     * @return GpioDevelop string GPIO value or boolean false
     */
    public function setup($pinNo, $direction);

    /**
     * Get input value
     *
     * @access public
     * @param  int   $pinNo
     *
     * @return integer string GPIO value or boolean false
     */
    public function input($pinNo);

    /**
     * Set output value
     *
     * @access public
     * @param  int    $pinNo
     * @param  string $value
     *
     * @return GpioDevelop Gpio current instance or boolean false
     */
    public function output($pinNo, $value);

    /**
     * Unexport Pin
     *
     * @access public
     * @param  int $pinNo
     *
     * @return GpioDevelop Gpio current instance or boolean false
     */
    public function unexport($pinNo);

    /**
     * Unexport all pins
     *
     * @access public
     * @return GpioDevelop Gpio current instance or boolean false
     */
    public function unexportAll();

    /**
     * Check if pin is exported
     *
     * @access public
     * @param  int $pinNo
     *
     * @return boolean
     */
    public function isExported($pinNo);

    /**
     * get the pin's current direction
     *
     * @access public
     * @param  int $pinNo
     *
     * @return string string pin's direction value or boolean false
     */
    public function currentDirection($pinNo);

    /**
     * Check for valid direction, in or out
     *
     * @access public
     * @param  string $direction
     *
     * @return boolean
     */
    public function isValidDirection($direction);

    /**
     * Check for valid output value
     *
     * @access public
     * @param  mixed $output
     *
     * @return boolean
     */
    public function isValidOutput($output);

    /**
     * Check for valid pin value
     *
     * @access public
     * @param  int $pinNo
     *
     * @return boolean
     */
    public function isValidPin($pinNo);

    public function readValuePin($pinNo);
}

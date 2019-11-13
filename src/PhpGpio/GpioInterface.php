<?php

namespace PhpGpio;

/**
 * Gpio interface
 *
 * @author Vaidas Lažauskas <vaidas@notrix.lt>
 */
interface GpioInterface
{
    const DIRECTION_IN = 'in';
    const DIRECTION_OUT = 'out';

    const IO_VALUE_ON = 1;
    const IO_VALUE_OFF = 0;

    const PATH_GPIO = '/sys/class/gpio/gpio';
    const PATH_EXPORT = '/sys/class/gpio/export';
    const PATH_UNEXPORT = '/sys/class/gpio/unexport';

    /**
     * getHackablePins : the pins you can hack with.
     * @link http://elinux.org/RPi_Low-level_peripherals
     *
     * @return array
     */
    public function getHackablePins(): array;

    /**
     * Setup pin, takes pin number and direction (in or out)
     *
     * @param int $pinNo
     * @param string $direction
     *
     * @return GpioDevelop string GPIO value or boolean false
     */
    public function setup(int $pinNo, string $direction);

    /**
     * Get input value
     *
     * @param int $pinNo
     *
     * @return string GPIO value
     */
    public function input(int $pinNo): string;

    /**
     * Set output value
     *
     * @param int $pinNo
     * @param string $output
     *
     * @return GpioDevelop Gpio current instance or boolean false
     */
    public function output(int $pinNo, string $output): GpioInterface;

    /**
     * Unexport Pin
     *
     * @param int $pinNo
     *
     * @return GpioDevelop Gpio current instance or boolean false
     */
    public function unexport(int $pinNo);

    /**
     * Unexport all pins
     *
     * @return GpioInterface Gpio current instance
     */
    public function unexportAll(): GpioInterface;

    /**
     * Check if pin is exported
     *
     * @param int $pinNo
     *
     * @return boolean
     */
    public function isExported(int $pinNo);

    /**
     * get the pin's current direction
     *
     * @param int $pinNo
     *
     * @return string string pin's direction value or boolean false
     */
    public function currentDirection(int $pinNo);

    /**
     * Check for valid direction, in or out
     *
     * @param string $direction
     */
    public function isValidDirection(string $direction);

    /**
     * Check for valid output value
     *
     * @param string $output
     *
     * @return boolean
     */
    public function isValidOutputOrException(string $output);

    /**
     * Check for valid pin value
     *
     * @param int $pinNo
     *
     * @return boolean
     */
    public function isValidPinOrException(int $pinNo);
}

<?php

namespace PhpGpio;

/**
 * This Gpio class is for developing not in raspberry environment
 * There is no need for root user and special file structure.
 *
 * @author Vaidas Lažauskas <vaidas@notrix.lt>
 */
class GpioDevelop implements GpioInterface
{
    /**
     * @access public
     * @var    array
     */
    public $pins = [14, 15, 17, 18];

    /**
     * @access public
     * @var    array
     */
    public $hackablePins = [17, 18];

    /**
     * @access public
     * @var    int
     */
    public $inputValue = GpioInterface::IO_VALUE_OFF;

    /**
     * @access public
     * @var    string
     */
    public $direction = GpioInterface::DIRECTION_OUT;

    /**
     * getHackablePins : the pins you can hack with.
     *
     * @link   http://elinux.org/RPi_Low-level_peripherals
     * @access public
     * @return array
     */
    public function getHackablePins()
    {
        return $this->hackablePins;
    }

    /**
     * Setup pin, takes pin number and direction (in or out)
     *
     * @access public
     * @param  int    $pinNo
     * @param  string $direction
     *
     * @return GpioDevelop or boolean false
     */
    public function setup($pinNo, $direction)
    {
        return $this;
    }

    /**
     * Get input value
     *
     * @access public
     * @param  int   $pinNo
     *
     * @return int GPIO value or boolean false
     */
    public function input($pinNo)
    {
        return $this->inputValue;
    }

    /**
     * Set output value
     *
     * @access public
     * @param  int    $pinNo
     * @param  string $value
     *
     * @return GpioDevelop or boolean false
     */
    public function output($pinNo, $value)
    {
        return $this;
    }

    /**
     * Unexport Pin
     *
     * @access public
     * @param  int $pinNo
     *
     * @return GpioDevelop or boolean false
     */
    public function unexport($pinNo)
    {
        return $this;
    }

    /**
     * Unexport all pins
     *
     * @access public
     * @return GpioDevelop or boolean false
     */
    public function unexportAll()
    {
        return $this;
    }

    /**
     * Check if pin is exported
     *
     * @access public
     * @param  int $pinNo
     *
     * @return boolean
     */
    public function isExported($pinNo)
    {
        return in_array($pinNo, $this->pins) || in_array($pinNo, $this->hackablePins);
    }

    /**
     * get the pin's current direction
     *
     * @access public
     * @param  int $pinNo
     *
     * @return string pin's direction value or boolean false
     */
    public function currentDirection($pinNo)
    {
        return $this->direction;
    }

    /**
     * Check for valid direction, in or out
     *
     * @access public
     * @param  string $direction
     *
     * @return boolean
     */
    public function isValidDirection($direction)
    {
        return $direction == GpioInterface::DIRECTION_IN || $direction == GpioInterface::DIRECTION_OUT;
    }

    /**
     * Check for valid output value
     *
     * @access public
     * @param  mixed $output
     *
     * @return boolean
     */
    public function isValidOutput($output)
    {
        return $output == GpioInterface::IO_VALUE_ON || $output == GpioInterface::IO_VALUE_OFF;
    }

    /**
     * Check for valid pin value
     *
     * @access public
     * @param  int $pinNo
     *
     * @return boolean
     */
    public function isValidPin($pinNo)
    {
        return in_array($pinNo, $this->pins) || in_array($pinNo, $this->hackablePins);
    }
}

<?php

namespace PhpGpio;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Gpio implements GpioInterface
{
    // Using BCM pin numbers.
    private $pins;
    private $hackablePins;

    /**
     * Class constructor
     *
     * @access public
     * @link   http://www.raspberrypi-spy.co.uk/2012/06/simple-guide-to-the-rpi-gpio-header-and-pins/
     */
    public function __construct()
    {
        // Raspberry Pi utils?
        $raspi = new Pi;

        // Logger
        $logger = new Logger('Raspberry Pi');
        $logger->pushHandler(new StreamHandler('logs/'. date('d-m-Y') .'.log', Logger::WARNING));

        if ($raspi->getVersion() < 4) {
            $this->pins = [
                0, 1, 4, 7, 8, 9,
                10, 11, 14, 15, 17, 18,
                21, 22, 23, 24, 25
            ];
            $this->hackablePins = [
                4, 7, 8, 9,
                10, 11, 17, 18,
                21, 22, 23, 24, 25
            ];
        } else if($raspi->getVersion() < 16) {
            # new GPIO layout (REV2)
            $this->pins = [
                2, 3, 4, 7, 8, 9,
                10, 11, 14, 15, 17, 18,
                22, 23, 24, 25, 27
            ];
            $this->hackablePins = [
                4, 7, 8, 9,
                10, 11, 17, 18,
                22, 23, 24, 25, 27
            ];
        } else {
            # new GPIO layout (B+)
            $this->pins = [
                2, 3, 4, 5, 6, 7,
                8, 9, 10, 11, 12, 13,
                14, 15, 16, 17, 18, 19,
                20, 21, 22, 23, 24, 25,
                26, 27
            ];
            $this->hackablePins = [
                4, 5, 6,
                12, 13, 16, 17, 18, 19,
                20, 21, 22, 23, 24, 25, 26, 27
            ];
        }
    }

    /**
     * getHackablePins : the pins you can hack with.
     *
     * @access public
     * @link   http://elinux.org/RPi_Low-level_peripherals
     * @return integer[]
     */
    public function getHackablePins()
    {
        return $this->hackablePins;
    }

    private $directions = [
        GpioInterface::DIRECTION_IN,
        GpioInterface::DIRECTION_OUT,
    ];

    private $outputs = [
        GpioInterface::IO_VALUE_ON,
        GpioInterface::IO_VALUE_OFF,
    ];

    // exported pins for when we unexport all
    private $exportedPins = [ ];

    /**
     * Setup pin, takes pin number and direction (in or out)
     *
     * @access public
     * @param  int    $pinNo
     * @param  string $direction
     * @return mixed  string GPIO value or boolean false
     */
    public function setup($pinNo, $direction)
    {
        if (!$this->isValidPin($pinNo)) {
            return false;
        }

        // if exported, unexport it first
        if ($this->isExported($pinNo)) {
            $this->unexport($pinNo);
        }

        // Export pin
        file_put_contents(GpioInterface::PATH_EXPORT, $pinNo);

        // if valid direction then set direction
        if ($this->isValidDirection($direction)) {
            file_put_contents(GpioInterface::PATH_GPIO.$pinNo.'/direction', $direction);
        }

        // Add to exported pins array
        $this->exportedPins[] = $pinNo;

        return $this;
    }

    /**
     * Get input value
     *
     * @access public
     * @param  int $pinNo
     * @return false|string string GPIO value or boolean false
     * @throws \Exception
     */
    public function input($pinNo)
    {
        if (!$this->isValidPin($pinNo)) {
            return false;
        }
        if ($this->isExported($pinNo)) {
            if ($this->currentDirection($pinNo) != "out") {
                return trim(file_get_contents(GpioInterface::PATH_GPIO.$pinNo.'/value'));
            }

            $this->logger->addError('Error!' . $this->currentDirection($pinNo) . ' is a wrong direction for this pin!');
            throw new \Exception('Error!' . $this->currentDirection($pinNo) . ' is a wrong direction for this pin!');
        }

        return false;
    }

    /**
     * Set output value
     *
     * @access public
     * @param  int $pinNo
     * @param  string $value
     * @return mixed Gpio current instance or boolean false
     * @throws \Exception
     */
    public function output($pinNo, $value)
    {
        if (!$this->isValidPin($pinNo)) {
            return false;
        }
        if (!$this->isValidOutput($value)) {
            return false;
        }
        if ($this->isExported($pinNo)) {
            if ($this->currentDirection($pinNo) != "in") {
                file_put_contents(GpioInterface::PATH_GPIO.$pinNo.'/value', $value);
            } else {
                $this->logger->addError('Error! Wrong Direction for this pin! Meant to be out while it is ' . $this->currentDirection($pinNo));
                throw new \Exception('Error! Wrong Direction for this pin! Meant to be out while it is ' . $this->currentDirection($pinNo));
            }
        }

        return $this;
    }

    /**
     * Unexport Pin
     *
     * @access public
     * @param  int   $pinNo
     * @return mixed Gpio current instance or boolean false
     */
    public function unexport($pinNo)
    {
        if (!$this->isValidPin($pinNo)) {
            return false;
        }
        if ($this->isExported($pinNo)) {
            file_put_contents(GpioInterface::PATH_UNEXPORT, $pinNo);
            foreach ($this->exportedPins as $key => $value) {
                if($value == $pinNo) unset($key);
            }
        }

        return $this;
    }

    /**
     * Unexport all pins
     *
     * @access public
     * @return Gpio Gpio current instance or boolean false
     */
    public function unexportAll()
    {
        foreach ($this->exportedPins as $pinNo) {
            file_put_contents(GpioInterface::PATH_UNEXPORT, $pinNo);
        }
        $this->exportedPins = [ ];

        return $this;
    }

    /**
     * Check if pin is exported
     *
     * @access public
     * @param  int, $pinNo
     * @return boolean
     */
    public function isExported($pinNo)
    {
        if (!$this->isValidPin($pinNo)) {
            return false;
        }

        return file_exists(GpioInterface::PATH_GPIO.$pinNo);
    }

    /**
     * get the pin's current direction
     *
     * @access public
     * @param  int, $pinNo
     * @return false|string string pin's direction value or boolean false
     */
    public function currentDirection($pinNo)
    {
        if (!$this->isValidPin($pinNo)) {
            return false;
        }

        return trim(file_get_contents(GpioInterface::PATH_GPIO.$pinNo.'/direction'));
    }

    /**
     * Check for valid direction, in or out
     *
     * @access    public
     * @param     string, $direction
     * @exception InvalidArgumentException
     * @return    boolean true
     */
    public function isValidDirection($direction)
    {
        if (!is_string($direction) || empty($direction)) {
            $this->logger->addError('Direction "%s" is invalid (string expected).', $direction);
            throw new \InvalidArgumentException(sprintf('Direction "%s" is invalid (string expected).', $direction));
        }
        if (!in_array($direction, $this->directions)) {
            $this->logger->addError('Direction "%s" is invalid (unknown direction).', $direction);
            throw new \InvalidArgumentException(sprintf('Direction "%s" is invalid (unknown direction).', $direction));
        }

        return true;
    }

    /**
     * Check for valid output value
     *
     * @access    public
     * @param     mixed, $output
     * @exception InvalidArgumentException.
     * @return    boolean true
     */
    public function isValidOutput($output)
    {
        if (!is_int($output)) {
            $logger->addError('Pin value "%s" is invalid (integer expected).', $output);
            throw new \InvalidArgumentException(sprintf('Pin value "%s" is invalid (integer expected).', $output));
        }
        if (!in_array($output, $this->outputs)) {
            $logger->addError('Output value "%s" is invalid (out of exepected range).', $output);
            throw new \InvalidArgumentException(sprintf('Output value "%s" is invalid (out of exepected range).', $output));
        }

        return true;
    }

    /**
     * Check for valid pin value
     *
     * @access    public
     * @param     int, $pinNo
     * @exception InvalidArgumentException
     * @return    boolean true
     */
    public function isValidPin($pinNo)
    {
        if (!is_int($pinNo)) {
            $logger->addError('Pin number "%s" is invalid (integer expected).', $pinNo);
            throw new \InvalidArgumentException(sprintf('Pin number "%s" is invalid (integer expected).', $pinNo));
        }
        if (!in_array($pinNo, $this->pins)) {
            $logger->addError('Pin number "%s" is invalid (out of exepected range).', $pinNo);
            throw new \InvalidArgumentException(sprintf('Pin number "%s" is invalid (out of exepected range).', $pinNo));
        }

        return true;
    }

    public function readValuePin($pinNo) {
        if (!$this->isValidPin($pinNo)) {
            return false;
        }

        return trim(file_get_contents(GpioInterface::PATH_GPIO.$pinNo.'/value'));;
    }
}

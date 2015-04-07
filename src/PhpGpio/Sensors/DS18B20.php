<?php

namespace PhpGpio\Sensors;

use Monolog\Logger;
use Monolog\Handler\StreamHandler

/*
 * 1-Wire is a device communications bus system designed by Dallas Semiconductor Corp.
 * that provides low-speed data, signaling, and power over a single signal.
 * 1-Wire is similar in concept to I²C, but with lower data rates and longer range.
 * It is typically used to communicate with small inexpensive devices
 * such as digital thermometers and weather instruments.
 * (source : http://en.wikipedia.org/wiki/1-Wire)
 */

class DS18B20 implements SensorInterface
{

    private $bus = null; // ex: '/sys/bus/w1/devices/28-000003ced8f4/w1_slave'
    const BASEPATH = '/sys/bus/w1/devices/28-';


    /**
     * Class Constructor
     */
     public function __construct()
     {
         // Logger
         $logger = new Logger('Raspberry Pi');
         $logger->pushHandler(new StreamHandler('logs/'. date('d-m-Y') .'.log', Logger::WARNING));
     }

    /**
     * Get-Accesssor
     *
     * @access public
     */
    public function getBus()
    {
        return $this->bus;
    }

    /**
     * Set-Accesssor
     *
     * @access public
     * @param  $value
     */
    public function setBus($value)
    {
        // ? is a non empty string, & a valid file path
        if (empty($value) || !is_string($value) || !file_exists($value)) {
            $logger->addError("$value is not a valid w1 bus path.");
            throw new \InvalidArgumentException("$value is not a valid w1 bus path");
        }

        // ? is a regular w1-bus path on a Raspbery ?
        if (!strstr($value, self::BASEPATH)) {
            $logger->addError("$value does not seem to be a regular w1 bus path");
            throw new \InvalidArgumentException("$value does not seem to be a regular w1 bus path");
        }

        $this->bus = $value;
    }

    /**
     * Setup
     *
     * @access public
     * @return $this
     */
    public function __construct()
    {
        $this->bus = $this->guessBus();

        return $this;
    }

    /**
     * guessBus: Guess the thermal sensor bus folder path
     *
     * the directory 28-*** indicates the DS18B20 thermal sensor is wired to the bus
     * (28 is the family ID) and the unique ID is a 12-chars numerical digit
     *
     * @access public
     * @return string $busPath
     */
    public function guessBus()
    {
        $busFolders = glob(self::BASEPATH . '*'); // predictable path on a Raspberry Pi
        if (0 === count($busFolders)) {
            return false;
        }
        $busPath = $busFolders[0]; // get the first thermal sensor found

        return $busPath . '/w1_slave';
    }

    /**
     * Read
     *
     * @access public
     * @param  array $args
     * @return float $value
     */
    public function read($args = [ ])
    {
        if (!is_string($this->bus) || !file_exists($this->bus)) {
            $logger->addError('No bus file found: please run sudo modprobe w1-gpio; sudo modprobe w1-therm & check the guessBus() method result');
            throw new \Exception("No bus file found: please run sudo modprobe w1-gpio; sudo modprobe w1-therm & check the guessBus() method result");
        }
        $raw = file_get_contents($this->bus);
        $raw = str_replace("\n", "", $raw);
        $boom = explode('t=',$raw);

        return floatval($boom[1]/1000);
    }

    /**
     * Write
     *
     * @access public
     * @param  array $args
     * @return boolean
     */
    public function write($args = [ ])
    {
        return false;
    }

}

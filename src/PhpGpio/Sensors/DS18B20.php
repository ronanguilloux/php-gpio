<?php

namespace PhpGpio\Sensors;

/*
 * 1-Wire is a device communications bus system designed by Dallas Semiconductor Corp.
 * that provides low-speed data, signaling, and power over a single signal.
 * 1-Wire is similar in concept to I²C, but with lower data rates and longer range.
 * It is typically used to communicate with small inexpensive devices
 * such as digital thermometers and weather instruments.
 * (source : http://en.wikipedia.org/wiki/1-Wire)
 */

use Exception;
use InvalidArgumentException;
use PhpGpio\FileGetContentsService;

class DS18B20 implements SensorInterface
{
    const BASEPATH = '/sys/bus/w1/devices/28-'; // ex: '/sys/bus/w1/devices/28-000003ced8f4/w1_slave'

    /** @var string */
    protected $bus;

    /**
     * Setup
     */
    public function __construct()
    {
        $this->bus = $this->guessBus();
    }

    /**
     * guessBus: Guess the thermal sensor bus folder path
     *
     * the directory 28-*** indicates the DS18B20 thermal sensor is wired to the bus
     * (28 is the family ID) and the unique ID is a 12-chars numerical digit
     *
     * @return string $busPath
     */
    public function guessBus(): string
    {
        $busFolders = glob(self::BASEPATH . '*'); // predictable path on a Raspberry Pi
        if ($busFolders === false || 0 === count($busFolders)) {
            throw new Exception('No bus found');
        }
        $busPath = $busFolders[0]; // get the first thermal sensor found

        return $busPath . '/w1_slave';
    }

    public function getBus(): string
    {
        return $this->bus;
    }

    /**
     * @param string $value
     */
    public function setBus(string $value)
    {
        // ? is a non empty string, & a valid file path
        if (empty($value) || !is_string($value) || !file_exists($value)) {
            throw new InvalidArgumentException("$value is not a valid w1 bus path");
        }

        // ? is a regular w1-bus path on a Raspbery ?
        if (!strstr($value, self::BASEPATH)) {
            throw new InvalidArgumentException("$value does not seem to be a regular w1 bus path");
        }

        $this->bus = $value;
    }

    /**
     * Read
     *
     * @param array $args
     * @return float $value
     * @throws Exception
     */
    public function read($args = []): float
    {
        if (!is_string($this->bus) || !file_exists($this->bus)) {
            throw new Exception("No bus file found: please run sudo modprobe w1-gpio; sudo modprobe w1-therm & check the guessBus() method result");
        }
        $raw = FileGetContentsService::get($this->bus);
        $raw = str_replace("\n", '', $raw);
        $boom = explode('t=', $raw);

        return floatval($boom[1] / 1000);
    }
}

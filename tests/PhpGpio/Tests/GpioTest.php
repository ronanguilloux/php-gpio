<?php

namespace PhpGpio\Tests;

use PhpGpio\Gpio;

class GpioTest extends \PhpUnit_Framework_TestCase
{
    private $gpio;
    private $rpi ='raspberrypi';
    private $hackablePins = array();

    public function setUp()
    {
        $this->gpio = new Gpio();

        // defaut should be: $this->hackablePins = $this->gpio->getHackablePins();
        // but in this test set, the Raspi is wired to a breadboard
        // and the 4th Gpio pin is reserved to read the DS18B20 sensor.
        // Other available gpio pins are connected to LEDs
        $this->hackablePins = array(
           17, 18, 21, 22, 23,24, 25
       );
    }

    /**
     * @outputBuffering enabled
     */
    public function assertPreconditionOrMarkTestSkipped()
    {
        if ($this->rpi !== $nodename = exec('uname --nodename')) {
            $warning = sprintf(" Precondition is not met : %s is not a %s machine! ", $nodename, $this->rpi);
            $this->markTestSkipped($warning);
        }
    }

    /**
     * Setting up gpio pins
     */
    public function testSetupWithRightParamaters()
    {
        $this->assertPreconditionOrMarkTestSkipped();
        foreach ($this->hackablePins as $pin) {
            $result = $this->gpio->setup($pin, 'out');
            $this->assertTrue($result instanceof Gpio);
        }
    }

    /**
     * Outputting gpio pins (ON)
     * @depends testSetupWithRightParamaters
     */
    public function testOutPutWithRightParametersOn()
    {
        foreach ($this->hackablePins as $pin) {
            $result = $this->gpio->output($pin, 1);
            $this->assertTrue($result instanceof Gpio);
        }
    }

    /**
     * Outputting gpio pins (OFF)
     * @depends testOutPutWithRightParametersOn
     */
    public function testOutPutWithRightParametersOut()
    {
        sleep(1);
        foreach ($this->hackablePins as $pin) {
            $result = $this->gpio->output($pin, 0);
            $this->assertTrue($result instanceof Gpio);
        }
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetupWithNegativePinAndRightDirection()
    {
        $this->gpio->setup(-1, 'out');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetupWithNullPinAndRightDirection()
    {
        $this->gpio->setup(null, 'out');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetupWithWrongPinAndRightDirection()
    {
        $this->gpio->setup('wrongPin', 'out');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetupWithRightPinAndWrongDirection()
    {
        $this->assertPreconditionOrMarkTestSkipped();
        $this->gpio->setup(17, 'wrongDirection');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetupWithRightPinAndNullDirection()
    {
        $this->assertPreconditionOrMarkTestSkipped();
        $this->gpio->setup(17, null);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetupWithMissingArguments()
    {
        $this->gpio->setup(17);
    }

}

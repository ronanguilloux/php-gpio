<?php

namespace PhpGpio\Tests\Sensors;

use PhpGpio\Sensors\DS18B20;

/**
 * @author Ronan Guilloux <ronan.guilloux@gmail.com>
 */
class DS18B20Test extends \PhpUnit_Framework_TestCase
{
    private $sensor;
    private $rpi = 'raspberrypi';

    public function setUp()
    {
        $this->sensor = new DS18B20();
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
     * @expectedException InvalidArgumentException
     */
    public function testSetBusWithWrongNonExisitingFilePath()
    {
        //$this->assertPreconditionOrMarkTestSkipped();
        $result = $this->sensor->setBus('/foo/bar/.baz');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetBusWithWrongNullParameter()
    {
        $result = $this->sensor->setBus(null);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetBusWithWrongExistingFile()
    {
        $result = $this->sensor->setBus('/etc/hosts');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetBusWithWrongStringParameter()
    {
        $result = $this->sensor->setBus('foo');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetBusWithWrongIntParameter()
    {
        $result = $this->sensor->setBus(1);
    }

    /**
     * a valid guessBus test
     */
    public function testValidGuessBus()
    {
        $this->assertPreconditionOrMarkTestSkipped();
        $result = $this->sensor->guessBus();
        $this->assertTrue(file_exists((string)$result));
    }

    /**
     * a valid read test
     */
    public function testRead()
    {
        $this->assertPreconditionOrMarkTestSkipped();
        $result = $this->sensor->read();
        $this->assertTrue(is_float($result));
    }
}

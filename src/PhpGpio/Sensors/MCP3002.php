<?php

namespace PhpGpio\Sensors;

/**
 * The MCP3002 has a 10-bit analog to digital converter (ADC) with a simple to use SPI interface.
 *
 * @author <paolo@casarini.org>
 */
class MCP3002 implements SensorInterface {

    private $_clockpin;
    private $_mosipin;
    private $_misopin;
    private $_cspin;

    private $_gpio;

    /**
     * Constructor with the sused GPIOs.
     *
     * Use:
     * $adc = new MCP3002(11, 10, 9, 8);
     * $value = adc->read(array('channel' => 0));
     * echo $value;
     *
     * @access public
     * @param  integer $clockpin The clock (CLK) pin (ex. 11)
     * @param  integer $mosipin The Master Out Slave In (MOSI) pin (ex. 10)
     * @param  integer $misopin The Master In Slave Out (MISO) pin (ex. 9)
     * @param  integer $cspin The Chip Select (CSna) pin (ex. 8)
     */
    public function __construct($clockpin, $mosipin, $misopin, $cspin) {
        $this->_gpio = new GPIO();

        $this->_clockpin = $clockpin;
        $this->_mosipin = $mosipin;
        $this->_misopin = $misopin;
        $this->_cspin = $cspin;

        $this->_gpio->setup($this->_mosipin, "out");
        $this->_gpio->setup($this->_misopin, "in");
        $this->_gpio->setup($this->_clockpin, "out");
        $this->_gpio->setup($this->_cspin, "out");
    }

    /**
     * Read the specified channel.
     * You should specify the channel (0|1) to read with the <tt>channel</tt> argument.
     *
     * @access public
     * @param  array $args
     * @return integer
     */
    public function read($args = [ ]) {
        $channel = $args['channel'];
        if (!is_integer($channel) || !in_array($channel, [0, 1])) {
            echo $msg = "Only 2 channels are available on a Mcp3002: 0 or 1";
            throw new \InvalidArgumentException($msg);
        }

        // init comm
        $this->_gpio->output($this->_cspin, 1);
        $this->_gpio->output($this->_clockpin, 0);
        $this->_gpio->output($this->_cspin, 0);

        // channel selection
        $cmdout = (6 + $channel) << 5;
        for ($i = 0; $i < 3; $i++) {
            if ($cmdout & 0x80) {
                $this->_gpio->output($this->_mosipin, 1);
            } else {
                $this->_gpio->output($this->_mosipin, 0);
            }
            $cmdout <<= 1;
            $this->_gpio->output($this->_clockpin, 1);
            $this->_gpio->output($this->_clockpin, 0);
        }

        $adcout = 0;
        //  read in one empty bit, one null bit and 10 ADC bits
        for ($i = 0; $i < 12; $i++) {
            $this->_gpio->output($this->_clockpin, 1);
            $this->_gpio->output($this->_clockpin, 0);
            $adcout <<= 1;
            if ($this->_gpio->input($this->_misopin)) {
                $adcout |= 0x1;
            }
        }

        $this->_gpio->output($this->_cspin, 1);
        return $adcout >> 1;
    }

    /**
     * write
     *
     * @access public
     * @param  array, $args
     * @return bool?
     */
    public function write($args = [ ]) {
        return false;
    }
}

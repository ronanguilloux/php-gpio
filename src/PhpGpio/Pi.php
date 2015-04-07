<?php

namespace PhpGpio;

class Pi
{
    /**
     *
     * Get RaspberryPi version
     *
     * A list of Model and Pi Revision & Hardware Revision Code from '/proc/cpuinfo' is here:
     *
     * @access public
     * @link   http://www.raspberrypi-spy.co.uk/2012/09/checking-your-raspberry-pi-board-version/
     * @return decimal Raspi version
     */
    public function getVersion()
    {
        $cpuinfo = preg_split ("/\n/", file_get_contents('/proc/cpuinfo'));
        foreach ($cpuinfo as $line) {
            if (preg_match('/Revision\s*:\s*([^\s]*)\s*/', $line, $matches)) {
                return hexdec($matches[1]);
            }
        }

        return 0;
    }

    /**
     * Get the CPu load of the Raspberry.
     *
     * @access public
     * @return string.
     */
    public function getCpuLoad()
    {
        return sys_getloadavg();
    }

    /**
     * Get the Cpu temperature
     *
     * @access public
     * @param  $fahrenheit, boolean
     * @return integer.
     */
    public function getCpuTemp($fahrenheit = false)
    {
        $cputemp = floatval(file_get_contents('/sys/class/thermal/thermal_zone0/temp'))/1000;

		if($fahrenheit) {
			$cputemp = 1.8* $cputemp+32;

        return $cputemp;

      }
    }

    /**
     * Get the temperature of the Graphical Porcessing Unit.
     *
     * @access public
     * @param  $fahrenheit, boolean
     * @return integer.
     */
    public function getGpuTemp($fahrenheit = false)
    {
        $gputemp = floatval(str_replace(['temp=', '\'C'], '', exec('/opt/vc/bin/vcgencmd measure_temp')));

		if($fahrenheit) {
			$gputemp = 1.8* $gputemp+32;

        return $gputemp;

      }
    }

    /**
     *  Get the frequency of the CPU.
     *
     * @access public
     * @return integer
     */
    public function getCpuFrequency()
    {
        $frequency = floatval(file_get_contents('/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq'))/1000;

        return $frequency;
    }
}

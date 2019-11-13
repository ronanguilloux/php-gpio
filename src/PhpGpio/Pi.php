<?php

namespace PhpGpio;

class Pi
{
    /**
     * Get RaspberryPi version
     *
     * A list of Model and Pi Revision & Hardware Revision Code from '/proc/cpuinfo' is here:
     * @link http://www.raspberrypi-spy.co.uk/2012/09/checking-your-raspberry-pi-board-version/
     *
     * @return int Raspi version
     */
    public function getVersion(): int
    {
        $result = FileGetContentsService::get('/proc/cpuinfo');
        $cpuInfo = preg_split("/\n/", $result);

        if (!is_array($cpuInfo)) {
            return 0;
        }

        foreach ($cpuInfo as $line) {
            if (preg_match('/Revision\s*:\s*([^\s]*)\s*/', $line, $matches)) {
                return (int)hexdec($matches[1]);
            }
        }

        return 0;
    }

    public function getCpuLoad(): array
    {
        return sys_getloadavg();
    }

    public function getCpuTemp($fahrenheit = false): float
    {
        $cputemp = floatval(file_get_contents('/sys/class/thermal/thermal_zone0/temp')) / 1000;

        if ($fahrenheit) {
            $cputemp = 1.8 * $cputemp + 32;
        }

        return $cputemp;
    }

    public function getGpuTemp($fahrenheit = false): float
    {
        $gputemp = floatval(str_replace(['temp=', '\'C'], '', exec('/opt/vc/bin/vcgencmd measure_temp')));

        if ($fahrenheit) {
            $gputemp = 1.8 * $gputemp + 32;
        }

        return $gputemp;
    }

    public function getCpuFrequency(): float
    {
        return floatval(file_get_contents('/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq')) / 1000;
    }
}

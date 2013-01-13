<?php

namespace PhpGpio;

class Pi
{
    public function getVersion()
    {
        $cpuinfo = preg_split ("/\n/", file_get_contents('/proc/cpuinfo'));
        foreach($cpuinfo as $line) {
            if(preg_match('/Revision\s*:\s*([^\s]*)\s*/', $line, $matches)) {
                return hexdec($matches[1]) & 0xf;
            }
        }
        return 0;
    }
}
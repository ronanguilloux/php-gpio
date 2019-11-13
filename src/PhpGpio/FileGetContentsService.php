<?php

namespace PhpGpio;

class FileGetContentsService
{
    /**
     * Abstract access to Filesystem for Tests
     *
     * @param string $path
     * @return string
     */
    static public function get(string $path): string
    {
        $result = file_get_contents($path);
        return ($result === false) ? '' : $result;
    }

}

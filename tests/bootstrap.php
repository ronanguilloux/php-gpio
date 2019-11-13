<?php

// Let's test if PHPUnit/Autoload is installed locally
// (by default in /usr/share/php/PHPUnit).
// This check allows you to use phpunit.phar alternatively
if (in_array('phpunit_autoload', spl_autoload_functions())) {
    require_once 'PHPUnit/Autoload.php';
}

$rpi = 'raspberrypi';
if ($rpi !== $nodename = exec('uname --nodename')) {
    $warning = sprintf("[WARN] %s is not a %s machine: not all tests can be run.", $nodename, $rpi);
    echo <<<EOT
$warning

EOT;
}

if ('root' !== $_SERVER['USER'] || empty($_SERVER['SUDO_USER'])) {
    $warning = sprintf("[ABORT] Please run this script as root: sudo phpunit or sudo ./phpunit.phar", $_SERVER['USER']);
    echo <<<EOT
$warning

EOT;
    die();
}

if (!extension_loaded('curl') || !function_exists('curl_init')) {
    die(<<<EOT
cURL has to be enabled!
EOT
    );
}

if (!($loader = @include __DIR__ . '/../vendor/autoload.php')) {
    die(<<<EOT
You need to install the project dependencies using Composer:
$ wget http://getcomposer.org/composer.phar
OR
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install --dev
$ phpunit

EOT
    );
}

$loader->add('PhpGpio\Tests', __DIR__);

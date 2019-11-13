php-gpio
========


**php-gpio** is a simple PHP library to play with the Raspberry PI's GPIO pins.

It provides simple tools such as reading & writing to pins.

[![Latest Stable Version](https://poser.pugx.org/ronanguilloux/php-gpio/v/stable.png)](https://packagist.org/packages/ronanguilloux/php-gpio) [![Build Status](https://secure.travis-ci.org/ronanguilloux/php-gpio.png?branch=master)](http://travis-ci.org/ronanguilloux/php-gpio) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ronanguilloux/php-gpio/badges/quality-score.png?s=199e653b7ec9627593843ba15c961f9c0be7701d)](https://scrutinizer-ci.com/g/ronanguilloux/php-gpio/) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/fde42adb-344d-4055-b78d-20b598040ac8/mini.png)](https://insight.sensiolabs.com/projects/fde42adb-344d-4055-b78d-20b598040ac8) [![Total Downloads](https://poser.pugx.org/ronanguilloux/php-gpio/downloads.png)](https://packagist.org/packages/ronanguilloux/php-gpio)

![Circuit snapshot](https://raw.github.com/ronanguilloux/temperature-pi/master/resources/images/mounting.jpg)

[UPDATE Fall 2014] Now compatible with both Raspberry Pi **B Model & B+ Revision**

Tl;dr
-----

```
- "Hey, I just want to blink a LED from my raspberry pi hosted website!"
- "OK good guy: git clone https://github.com/ronanguilloux/php-gpio-web.git`
   & remember to come back here when you're lost ;-)"
```

=> [php-gpio-web: a simple example for you to play with Leds & PHP](https://github.com/ronanguilloux/php-gpio-web)


GPIO
----

General Purpose Input/Output (a.k.a. GPIO) is a generic pin on a chip whose behavior
(including whether it is an input or output pin) can be controlled (programmed) through software.
The Raspberry Pi allows peripherals and expansion boards (such as the Rpi Gertboard)
to access the CPU by exposing the inputs and outputs.

For further informations about the Raspberry Pi's GPIO capabilities, see docs & schemas at http://elinux.org/RPi_Low-level_peripherals.

For Raspbeery Pi's GPIO controlling *LEDs*, have a look at a sample complete circuit diagram for a single LED,
with detailled explanations & schemas, [here](https://projects.drogon.net/raspberry-pi/gpio-examples/tux-crossing/gpio-examples-1-a-single-led/).

For Raspbeery Pi's GPIO controlling *sensors*, check the [DS18B20 (temperature sensor)](http://learn.adafruit.com/adafruits-raspberry-pi-lesson-11-ds18b20-temperature-sensing/overview) in action [here](https://github.com/ronanguilloux/temperature-pi)


Hardware prerequisites
----------------------

After having installed & wired your LED & resistor on a breadboard, 
add appropriate modules from the Linux Kernel:

For *LEDs*, enable the gpio module :

```bash
sudo modprobe w1-gpio
```

([see a complete circuit diagram for a single LED + explanations & schemas here](https://projects.drogon.net/raspberry-pi/gpio-examples/tux-crossing/gpio-examples-1-a-single-led/))

For *sensors*, enable the appropriate sensor.
By example for a [DS18B20 1-Wire digital temperature sensor](http://learn.adafruit.com/adafruits-raspberry-pi-lesson-11-ds18b20-temperature-sensing/overview):

```bash
sudo modprobe w1-therm
```

([see the DS18B20 in action on a Raspberry Pi here](https://github.com/ronanguilloux/temperature-pi))

To load such kernel modules automatically at boot time, edit the `/etc/modules` file & add these two lines:

```text
w1-gpio
w1-therm
```


Installation
------------

The recommended way to install php-gpio is through [composer](http://getcomposer.org).

Just run these three commands to install it

```bash
sudo apt-get install git
wget http://getcomposer.org/composer.phar
php composer.phar create-project --stability='dev' ronanguilloux/php-gpio intoYourPath
```

Now you can add the autoloader, and you will have access to the library:

```php
<?php

require 'vendor/autoload.php';
```

If you don't use neither **Composer** nor a _ClassLoader_ in your application, just require the provided autoloader:

```php
<?php

require_once 'src/autoload.php';
```


API Usage
---------

The API usage requires sudo permissions.  
To respect such permissions needs (say, for any web-related usage), see blinker file and the explanations below.

```php
<?php

require 'vendor/autoload.php';

use PhpGpio\Gpio;

echo "Setting up pin 17\n";
$gpio = new GPIO();
$gpio->setup(17, "out");

echo "Turning on pin 17\n";
$gpio->output(17, 1);

echo "Sleeping!\n";
sleep(3);

echo "Turning off pin 17\n";
$gpio->output(17, 0);

echo "Unexporting all pins\n";
$gpio->unexportAll();
```


Understanding I/O permissions
-----------------------------

Permissions make sense:
* it's bad practice to run your webserver user (say, Apache2's www-data) as `root`
* it's bad practice to `chmod 777 /dev` only because someone wants to blink a led freely

Such practices are regularly proposed on RPi forums, but they aren't security-aware 
& therefore not recommendable in an Internet environment.
Instead, the good old `/etc/sudoers` file allow your linux users to execute single files 
with sudo permissions without password to type.


The blinker file solution ("one-file-to-blink-them-all")
--------------------------------------------------------

In a PHP-based project, the API can only be used with sudo permissions. But there is a solution to avoid exposing your Raspbery Pi to security issues : Preparing & packaging inside an API client, in a single PHP file, the GPIO operation you need to run. In a hardware-based project, such operations are usualy few in number: blink a led, run a servomotor, etc. Such single PHP file containing your GPIO-related action can be called with determinated parameters from within your web-based application using `exec()` command : 

```php
(...)
$result = exec('sudo -t /usr/bin/php ./blinker 17 20000'); // calling the API client file
(...)
```

Such one-single PHP file to act as an API client for one GPIO action makes easier to configure specific sudo permissions in your `/etc/sudoers` file, as you'll see below. If you have more hardware operations to run (say, a LED + a servomotor + 2-3 sensors), more dedicated API client files, with their own parameters, is also very OK.

As an example of such solution, we provide a simple *blinker php file*, executable from the shell & from within your web based app. To run this blinker with sudo permissions but without password inputting,
just allow your `www-data` or your `pi` user to run the blinker script using `exec()`.
With the solution provided below, only one blinker script is needed to manage all your leds,
and your webserver application needs only one php file to be specified in `/etc/sudoers`.

This is the regular linux-file-permission-system way to do such things, not a dummy `chmod 777` bullshit.

Edit your `/etc/sudoers` file:

```bash
sudo visudo
```

Then add this two lines in your `/etc/sudoers` file :

```
    www-data ALL=NOPASSWD:/path/to/the/blinker
```

Replace `/path/to/the/blinker` with your single API client PHP file.

The blinker file provided now has the sufficient permissions & is ready to use the GPIO API. You do not need to install apache2-suexec nor suPHP.

You can test the blinker file solution with the `blinkerTest.php` file provided here:

```php
<?php

# blinkTester.php

# Blinks the LED wired to the GPIO #17 pin with 0.2 second delay:
$result = exec('sudo -t /usr/bin/php ./blinker 17 20000');
```

Test your blinker:

```bash
php blinkTester.php
```


API Implementations
-------------------

Some php-gpio api examples / demo :

* [Temperature-Pi](https://github.com/ronanguilloux/temperature-pi): a simple php project reading & logging temperatures using a DS18B20 1-Wire digital temperature sensor & this php-gpio library.
* [Php-Gpio-Web](https://github.com/ronanguilloux/php-gpio-web): a website damn simple integration example of the php-gpio lib

Unit Tests
----------

Running the full PhpUnit tests set over php-gpio requires a sudoable user, because of various gpio operations.
Instead of installing phpunit, you can just download & use the single PhpUnit package.
This can be easily done using `cURL`, to get the standalone PhpUnit's phar file:

```bash
wget http://pear.phpunit.de/get/phpunit.phar
chmod +x phpunit.phar
```
```bash
wget http://getcomposer.org/composer.phar
php composer.phar install --dev
```
```bash
sudo /usr/bin/php phpunit.phar
```


PHP Quality
-----------

For [PHP quality fans](http://phpqatools.org), and for my self coding improvement, I wrote a little script available in the ./bin directory I launch to check my PHP code: It produces various stats & metrics & improvements tips on the code.

Run tests

```bash
composer run tests
```

Credits
-------

* Aaron Pearce, for its [forked aaronpearce/PHP-GPIO project](https://github.com/aaronpearce/PHP-GPIO)
* Ronan Guilloux <ronan.guilloux@gmail.com>
* Bas Bloemsaat <bas@bloemsaat.com>, Raspberry Pi version dependency
* Alex Ciarlillo (@alexciarlillo), Raspberry Pi B+ revision support
* [All contributors](https://github.com/ronanguilloux/php-gpio/contributors)


License
-------

**php-gpio** is released under the MIT License.  
See the bundled LICENSE file for details.  
You can find a copy of this software here: https://github.com/ronanguilloux/php-gpio

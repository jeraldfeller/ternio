<?php
require 'vendor/autoload.php';
use Mailgun\Mailgun;
$mailgun = new Mailgun('key-7311f7481907ea2a8f278ea5144e3a24', new \Http\Adapter\Guzzle6\Client());
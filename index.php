<?php

require_once(__DIR__.'/library/Lethak/Frostbite/Server.php');

$Server = new Lethak_Frostbite_Server("109.239.158.44", 47200);
$Server->login("myRconPassword");

die('<pre>'.print_r('finished', true).'</pre>');



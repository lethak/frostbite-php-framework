<?php

require_once(__DIR__.'/library/Lethak/Frostbite/Server.php');

try
{
	$Server = new Lethak_Frostbite_Server("109.239.158.44", 47200);
	$Server->login("myRconPassword");
}
catch(Exception $error)
{
	die('Exception: <pre>'.print_r($error->getMessage(), true).'</pre>');
}

die('<pre>'.print_r('finished', true).'</pre>');



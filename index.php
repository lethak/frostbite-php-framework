<?php

require_once(__DIR__.'/library/Lethak/Frostbite/Server.php');

try
{
	$server = new Lethak_Frostbite_Server("109.239.158.44", 47200);
	$server->login("myRconPassword");
	$RESULT = $server->say('Hello World !')->players->getList();
}
catch(Exception $error)
{
	die('Exception: ['.get_class($error).'] <pre>'.print_r($error->getMessage(), true).'</pre>');
}

echo('FINISHED: <pre>'.print_r($RESULT, true).'</pre>');exit;


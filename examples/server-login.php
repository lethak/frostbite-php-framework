<?php
# ################################## #
#  LethaK's Frostbite-PHP-Framework  #
# ################################## #
#
# An open-source Framework to interact with Battlefield servers
#
# @author lethak https://github.com/lethak/frostbite-php-framework
#


echo('<h1>Example: SERVER LOGIN</h1><hr>');

require_once(dirname(__FILE__).'/../library/Lethak/Frostbite/Server.php');


	// change with your game-server IP, rcon port and valid rcon password
	$serverIp = '127.0.0.1';
	$serverRconPort = 47200;
	$serverRconPassword = 'myValidRconPassword';




/*********************************************************
	EXAMPLE 1 : Auth with valid credentials
*********************************************************/

try
{
	// THIS WILL WORK
	$server_A = new Lethak_Frostbite_Server($serverIp, $serverRconPort, $serverRconPassword);
	$server_A->login();
	if($server_A->isAuthed)
		echo('<br>AUTHED WITH SERV A');


}
catch(Exception $error)
{
	echo('<br>NOT AUTHED WITH SERV A');
	echo('<br>Example 1 Exception: ['.get_class($error).'] <pre>'.print_r($error->getMessage(), true).'</pre>');
}

echo('<hr>');





/*********************************************************
	EXAMPLE 2 : Auth with INVALID credentials
*********************************************************/

try
{
	// THIS WILL NOT WORK, the rconpassword is not valid obviously
	// will trigger an exception
	$server_B = new Lethak_Frostbite_Server($serverIp, $serverRconPort, 'myInvalidRconPassword');
	$server_B->login();
	if($server_B->isAuthed)
		echo('<br>AUTHED WITH SERV B');
}
catch(Lethak_Frostbite_Rcon_InvalidPassword_Exception $error)
{
	echo('<br>NOT AUTHED WITH SERV B (Credentials)');
	echo('<br>Example 2 Exception: ['.get_class($error).'] <pre>'.print_r($error->getMessage(), true).'</pre>');
}
catch(Exception $error)
{
	echo('<br>NOT AUTHED WITH SERV B (Exception)');
	echo('<br>Example 2 Exception: ['.get_class($error).'] <pre>'.print_r($error->getMessage(), true).'</pre>');
}

echo('<hr>');






/*********************************************************
	EXAMPLE 3 : rcon password inputed later
*********************************************************/

try
{
	$server_C = new Lethak_Frostbite_Server($serverIp, $serverRconPort);
	$server_C->login($serverRconPassword); // the rcon password is only inputed there (but not saved within the object!)
	if($server_C->isAuthed)
		echo('<br>AUTHED WITH SERV C');
}
catch(Exception $error)
{
	echo('<br>NOT AUTHED WITH SERV C');
	echo('<br>Example 3 Exception: ['.get_class($error).'] <pre>'.print_r($error->getMessage(), true).'</pre>');
}

echo('<hr>');






/*********************************************************
	EXAMPLE 4 : wrong IP !
*********************************************************/

try
{
	$server_D = new Lethak_Frostbite_Server('127.0.0.1', $serverRconPort);
	$server_D->login($serverRconPassword);
	if($server_D->isAuthed)
		echo('<br>AUTHED WITH SERV D');
}
catch(Lethak_Frostbite_Rcon_Connection_Exception $error)
{
	echo('<br>NOT AUTHED WITH SERV D');
	echo('<br>Example 4 Exception: ['.get_class($error).'] <pre>'.print_r($error->getMessage(), true).'</pre>');
}

echo('<br>END OF FILE');exit;


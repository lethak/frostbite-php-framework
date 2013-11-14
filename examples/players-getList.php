<?php
# ################################## #
#  LethaK's Frostbite-PHP-Framework  #
# ################################## #
#
# An open-source Framework to interact with Battlefield servers
#
# @author lethak https://github.com/lethak/frostbite-php-framework
#


echo('<h1>Example: Fetch the live player-list</h1><hr>');

require_once(dirname(__FILE__).'/../library/Lethak/Frostbite/Server.php');


	// change with your game-server IP, rcon port and valid rcon password
	$serverIp = '127.0.0.1';
	$serverRconPort = 47200;
	$serverRconPassword = 'myValidRconPassword';



/*********************************************************
	EXAMPLE 1 : PUBLIC player list (without login)
*********************************************************/

echo('<hr><br><h2>EXAMPLE 1</h2><br>');

try
{
	$server = new Lethak_Frostbite_Server($serverIp, $serverRconPort);
	if(!$server->isAuthed)
		echo('<br>NOT AUTHED WITH SERVER ');

	$RESULT = $server->players->getList();

	echo('<br>RESULT: <pre>'.print_r($RESULT, true).'</pre>');
}
catch(Exception $error)
{
	echo('<br>Exception: ['.get_class($error).'] <pre>'.print_r($error->getMessage(), true).'</pre>');
}


/*********************************************************
	EXAMPLE 2 : ADMIN player list (using login)
*********************************************************/

echo('<hr><br><h2>EXAMPLE 2</h2><br>');

try
{
	$server = new Lethak_Frostbite_Server($serverIp, $serverRconPort, $serverRconPassword);
	if(!$server->isAuthed)
		echo('<br>-NOT AUTHED WITH SERVER ');

	$server->login();
	$RESULT = $server->players->getList();
	
	# You can also chain calls like this;
	# $RESULT = $server->login()->players->getList();

	if($server->isAuthed)
		echo('<br>-AUTHED WITH SERVER ');


	echo('<br>RESULT: <pre>'.print_r($RESULT, true).'</pre>');
}
catch(Exception $error)
{
	echo('<br>Exception: ['.get_class($error).'] <pre>'.print_r($error->getMessage(), true).'</pre>');
}



echo('<hr>END OF FILE');


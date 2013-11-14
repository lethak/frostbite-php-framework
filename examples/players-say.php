<?php
# ################################## #
#  LethaK's Frostbite-PHP-Framework  #
# ################################## #
#
# An open-source Framework to interact with Battlefield servers
#
# @author lethak https://github.com/lethak/frostbite-php-framework
#


echo('<h1>Example: Fetch the live player-list and mess with them</h1><hr>');

require_once(dirname(__FILE__).'/../library/Lethak/Frostbite/Server.php');


	// change with your game-server IP, rcon port and valid rcon password
	$serverIp = '127.0.0.1';
	$serverRconPort = 47200;
	$serverRconPassword = 'myValidRconPassword';


/*********************************************************
	EXAMPLE  : toying with the whole server player list
*********************************************************/


try
{
	$server = new Lethak_Frostbite_Server($serverIp, $serverRconPort, $serverRconPassword);
	$server->login();

	// Fetching the player list...
	$playerList = $server->players->getList();

	// populating a blacklist, just for fun
	$blacklist = array('xXxXxAdolfHitlerxXxXx', '000MotherFracker000', 'LOL-YolO-SwAg-LOL'); //example only

	// Looping through the player list ...
	foreach ($playerList as $player)
	{
		// by default, $player is an instance of Lethak_Frostbite_Player

		$player->yell('Your name is: ! '.$player->name, 2);
		$player->say('Your ping is arround: '.$player->ping);
		$player->say('Your rank is: '.$player->rank);

		// If player name is found in our blacklist ..
		if(in_array($player->name, $blacklist))
		{
			$player->kill()->kick('Your name is blaclisted with this server');
			$player->say('HaHa! /Nelson'); // this line may trigger an Exception since $player may not be reachable anymore

			# $playerAsArray = $player->toArray(); // you can use toArray() to manipulate $player as an array instead
		}

		// you can also do this but less classy
		/*
		$server
			->players
				->say('Lorem Ipsum', array('player', '000MotherFracker000'))
				->yell('Dolor', 2,  array('player', '000MotherFracker000'))
				->kill('000MotherFracker000')
				->kick('000MotherFracker000')
		;
		*/
	}


	echo('<br>RESULT: <pre>'.print_r($RESULT, true).'</pre>');
}
catch(Exception $error)
{
	echo('<br>Exception: ['.get_class($error).'] <pre>'.print_r($error->getMessage(), true).'</pre>');
}



echo('<hr>END OF FILE');


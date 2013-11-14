<?php

require_once(dirname(__FILE__).'/../Server.php');

class Lethak_Frostbite_Server_Players
{
	/**
	 * @var Lethak_Frostbite_Server
	 */
	protected $server;

	function __construct(Lethak_Frostbite_Server &$Server)
	{
		$this->server = $Server;
	}

	public function get($playerName)
	{
		$list = $this->getList();
		foreach ($list['players'] as $player)
		{
			if($player->name==$playerName)
				return $player;
		}

		throw new Lethak_Frostbite_Rcon_InvalidPlayer_Exception($playerName);
		
	}

	/**
	 * Fetch the live player list from the game-server
	 * 
	 * If not authed, some info will be missing like the EA guid
	 * 
	 * @param bool $asObject (default: true) Return array of playerArray if false
	 * @throws Exception
	 * @return Array of Lethak_Frostbite_Player or array if $asObject=false
	 * 	(
	 * 	    [status] => OK
	 * 	    [players] => Array
	 * 	        (
	 * 	            [0] => Lethak_Frostbite_Player
	 * 	                (
	 * 	                    ...
	 * 	                )
	 * 
	 * 	            [1] => Lethak_Frostbite_Player
	 * 	                (
	 * 	                   ...
	 * 	                )
	 * 
	 * 	        )
	 * 
	 * 	    [playerCount] => 2
	 * 	)
	 */
	public function getList($asObject=true)
	{
		$this->server->connectionEnforcement();

		try
		{
			$response = $this->server->rconCommand('admin.listPlayers all');
		}
		catch(Lethak_Frostbite_Rcon_LogInRequired_Exception $error)
		{
			if(''.$this->server->rconPassword!='')
			{
				$this->login($this->server->rconPassword);
				return $this->getList($asObject);
			}
			else
			{
				$response = $this->server->rconCommand('listPlayers all');
			}
		}

		/* DEBUG RESPONSE:
		Array
		(
		    [0] => OK
		    [1] => 9 // $numberOfPlayerField

		    [2] => name
		    [3] => guid
		    [4] => teamId
		    [5] => squadId
		    [6] => kills
		    [7] => deaths (-1 if spectator)
		    [8] => score
		    [9] => rank (-1 if spectator)
		    [10] => ping

		    [11] => 4 // playercount

		    [12] => Empa_911
		    [13] => EA_86E5DEDEE0869812C5AFC591ED0529XX
		    [14] => 1
		    [15] => 1
		    [16] => 0
		    [17] => 4
		    [18] => 170
		    [19] => 16
		    [20] => 49
	
		    [21] => krogoth21
		    [22] => EA_3D2109D5E6DBB68E3802C60330FC93XX
		    [23] => 2
		    [24] => 1
		    [25] => 1
		    [26] => 3
		    [27] => 288
		    [28] => 22
		    [29] => 46

		    [?] => 0
		)
		*/

		$table['status'] = ''.$response[0];

		$numberOfPlayerField = intval($response[1]);

		unset($response[0], $response[1]);


		$table['players'] = array();
		$titles = array();
		$valueBuffer = array();
		$iPlayerField = 0;
		$iPlayerSet = 0;

		$iCursor = 2;

		// Fetching column title ...
		for ($i=0; $i < $numberOfPlayerField; $i++)
		{ 
			$titles[$i] = $response[$iCursor];
			$iCursor++;
		}

		// Fetching player count ...
		$table['playerCount'] = intval($response[$iCursor]);
		unset($response[$iCursor]);
		$iCursor++;
		
		// Fetching player set ...
		$playerSet = 0;
		while ($playerSet<$table['playerCount'])
		{
			$tempPlayer = array();
			for ($i=0; $i < $numberOfPlayerField; $i++)
			{ 
				$tempPlayer[$titles[$i]] = $response[$iCursor];
				unset($response[$iCursor]);
				if(count($tempPlayer)>=$numberOfPlayerField)
				{
					if($asObject)
						$tempPlayer = new Lethak_Frostbite_Player($tempPlayer, $this->server);
					$table['players'][] = $tempPlayer;
				}
				$iCursor++;
			}
			$playerSet++;
		}

		return $table;


	}



	/**
	 * Sending a chat message to the server or a player subset
	 *
	 * Displayed within the ingame chat window, prefixed by '[ADMIN]'
	 * Using rcon command: admin.say
	 *
	 * @param string $message
	 * @param string $playerSubset can be 'all', 'player <name>', 'team <id>', 'squad <teamid> <squadid>'
	 * @throws Exception
	 * @return Lethak_Frostbite_Server_Players
	 */
	public function say($message='', $playerSubset='all')
	{
		$message = trim((string)$message);
		$message = str_replace(array('"',"'"), '`', $message);
		if($message!=='')
		{
			$cmd = array_merge(array('admin.say', $message), explode(' ', $playerSubset));
			$response = $this->server->rconCommand($cmd);
		}
		return $this;
	}

	/**
	 * Sending a message to the server or a player subset,
	 *
	 * Displayed in front of the game-client screen for a specified duration
	 * Using rcon command: admin.yell
	 *
	 * @param string $message
	 * @param string $duration
	 * @param string $playerSubset can be 'all', 'player <name>', 'team <id>', 'squad <teamid> <squadid>'
	 * @throws Exception
	 * @return Lethak_Frostbite_Server_Players
	 */
	public function yell($message='', $duration=5, $playerSubset='all')
	{
		$message = trim((string)$message);
		$message = str_replace(array('"',"'"), '`', $message);
		if($message!=='')
		{
			$cmd = array_merge(array('admin.yell', $message, $duration), explode(' ', $playerSubset));
			$response = $this->server->rconCommand($cmd);
		}
		return $this;
	}

	/**
	 * Kick a player from the server
	 *
	 * Using rcon command: admin.kickPlayer
	 *
	 * @param string $playerName
	 * @param string $reason Displayed to the user as the reason why they were kicked (Optional)
	 * @throws Exception
	 * @return Lethak_Frostbite_Server_Players
	 */
	public function kick($playerName='', $reason='')
	{
		$playerName = str_replace(' ', '', $playerName);
		if ($playerName!='')
		{
			$cmd = array('admin.kickPlayer', $playerName, $reason);
			$response = $this->server->rconCommand($cmd);
		}
		return $this;
	}

	/**
	 * Kill a player without scoring effects
	 *
	 * Using rcon command: admin.killPlayer
	 *
	 * @param string $playerName
	 * @throws Exception
	 * @return Lethak_Frostbite_Server_Players
	 */
	public function killPlayer($playerName='')
	{
		$playerName = str_replace(' ', '', $playerName);
		if ($playerName!='')
		{
			$cmd = array('admin.killPlayer', $playerName);
			$response = $this->server->rconCommand($cmd);
		}
		return $this;
	}





}

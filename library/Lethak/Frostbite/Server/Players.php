<?php

require_once(__DIR__.'/../Server.php');

class Lethak_Frostbite_Server_Players
{
	private $server;

	function __construct(Lethak_Frostbite_Server &$Server)
	{
		$this->server = $Server;
	}


	/**
		@returns
		Array
		(
		    [status] => OK
		    [players] => Array
		        (
		            [0] => Array
		                (
		                    [name] => lethak
		                    [guid] => EA_5FF93997EB97B108B643F1513E0F4FXX
		                    [teamId] => 1
		                    [squadId] => 1
		                    [kills] => 0
		                    [deaths] => 0
		                    [score] => 0
		                    [rank] => 30
		                    [ping] => 21
		                )

		            [1] => Array
		                (
		                    [name] => FR-Akasid
		                    [guid] => EA_47292D6CA18E6051304C52A949EA18XX
		                    [teamId] => 2
		                    [squadId] => 1
		                    [kills] => 0
		                    [deaths] => 0
		                    [score] => 0
		                    [rank] => 24
		                    [ping] => 51
		                )

		        )

		    [playerCount] => 2
		)
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

		/*
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

		// Fetching column title
		for ($i=0; $i < $numberOfPlayerField; $i++)
		{ 
			$titles[$i] = $response[$iCursor];
			$iCursor++;
		}

		$table['playerCount'] = intval($response[$iCursor]);
		unset($response[$iCursor]);
		$iCursor++;
		
		// Fetching player set
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

}
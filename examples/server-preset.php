<?php
# ################################## #
#  LethaK's Frostbite-PHP-Framework  #
# ################################## #
#
# An open-source Framework to interact with Battlefield servers
#
# @author lethak https://github.com/lethak/frostbite-php-framework
#


echo('<h1>Example: working with server preset</h1><hr>');

require_once(dirname(__FILE__).'/../library/Lethak/Frostbite/Server.php');


	// change with your game-server IP, rcon port and valid rcon password
	$serverIp = '127.0.0.1';
	$serverRconPort = 47200;
	$serverRconPassword = 'myValidRconPassword';



/*********************************************************
	EXAMPLE  : creating your own preset class
*********************************************************/

require_once(dirname(__FILE__).'/../library/Lethak/Frostbite/Server/Preset/Abstract.php');

class myFastVehiclePreset extends Lethak_Frostbite_Server_Preset_Abstract
{

	public function label()
	{
		return 'Custom Fast Vehicle Preset';
	}

	protected function presetDefinition()
	{
		return array(
			'vars.friendlyFire' => 1,
			'vars.vehicleSpawnAllowed' => 1,
			'vars.vehicleSpawnDelay' => 20,
		);
	}
}



/*********************************************************
	EXAMPLE  : The server will apply your preset
*********************************************************/
# Please remember that most settings are in effect only after the current round restart

require_once(dirname(__FILE__).'/../library/Lethak/Frostbite/Server/Preset/Custom.php');
require_once(dirname(__FILE__).'/../library/Lethak/Frostbite/Server/Preset/Hardcore.php');

try
{
	$RESULT = array();

	$server = new Lethak_Frostbite_Server($serverIp, $serverRconPort, $serverRconPassword);
	$server->login();

	$fastVehiclePreset = new myFastVehiclePreset(); // see above example for the class definition
	$RESULT['myClassPreset'] = $fastVehiclePreset->applyTo($server);

	// Please note the following commented line is a shortcut for the above
	# $RESULT['myClassPreset'] = $server->apply($fastVehiclePreset);


	// Applying an embeded preset
	// if you have applyied another preset BEFORE this one, some variables will be overided obviously
	$RESULT['embededHardcorePreset'] = $server->apply(new Lethak_Frostbite_Server_Preset_Hardcore());


	// Applying a totally custom preset, defined on-the-fly
	$customVars = array(
		'vars.friendlyFire' => 0, 		// bool
		'vars.roundTimeLimit' => 100, 	// int (percent)
	);
	$customPreset = (new Lethak_Frostbite_Server_Preset_Custom($customVars);
	$RESULT['myCustomPreset'] = $server->apply($customPreset);



	echo('RESULT: <pre>'.print_r($RESULT, true).'</pre>');
}
catch(Exception $error)
{
	echo('<br>Exception: ['.get_class($error).'] <pre>'.print_r($error->getMessage(), true).'</pre>');
}



echo('<hr>END OF FILE');


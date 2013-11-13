<?php
require_once(dirname(__FILE__).'/Abstract.php');

class Lethak_Frostbite_Server_Preset_Deimos extends Lethak_Frostbite_Server_Preset_Abstract
{

	public function label()
	{
		return 'Deimos';
	}

	protected function presetDefinition()
	{
		return array(
			'vars.preset' => "Hardcore",

			//'vars.serverName' => "LethaK's Frostbite PHP Framework Server",
			'vars.serverMessage' => 'http://deim.fr',
			'vars.serverDescription' => 'Have fun, be nice !',
			//'vars.gamePassword' => '', // Disallowed on Ranked, ReadOnly
			//'vars.serverType' => 'Official', // Official, Ranked, Unranked, Private
			
			//'vars.alwaysAllowSpectators' => 1, // ReadOnly
			//'vars.miniMapSpotting' => 1, // ReadOnly
			//'vars.playerManDownTime' => 100, // ReadOnly
			//'vars.bulletDamage' => 100, // ReadOnly
			//'vars.crossHair' => 1, // UnknownCommand
			
			'vars.friendlyFire' => 1,
			'vars.teamKillCountForKick' => 5, // Disallowed on Ranked
			'vars.teamKillKickForBan' => 3,
			'vars.teamKillValueForKick' => 60*10,
			'vars.teamKillValueIncrease' => 60,
			'vars.teamKillValueDecreasePerSecond' => 1,
			'vars.idleTimeout' => 500,
			'vars.autoBalance' => 1,
			'vars.vehicleSpawnAllowed' => 1,
			'vars.vehicleSpawnDelay' => 50,
			'vars.regenerateHealth' => 1,
			'vars.onlySquadLeaderSpawn' => 0,
			'vars.miniMap' => 1,
			'vars.hud' => 1,
			'vars.3dSpotting' => 0,
			'vars.killCam' => 0,
			'vars.3pCam' => 0,
			'vars.nameTag' => 1,
			'vars.hitIndicatorsEnabled' => 1,
			'vars.playerRespawnTime' => 100,
			'vars.soldierHealth' => 69,
			'vars.forceReloadWholeMags' => 0,
			'vars.roundStartPlayerCount' => 4,
			'vars.commander' => 1, 
			'vars.gameModeCounter' => 100, 
			'vars.roundTimeLimit' => 0,
		);
	}
}

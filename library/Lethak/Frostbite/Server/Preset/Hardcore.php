<?php
require_once(dirname(__FILE__).'/Abstract.php');

class Lethak_Frostbite_Server_Preset_Hardcore extends Lethak_Frostbite_Server_Preset_Abstract
{

	public function label()
	{
		return 'Hardcore';
	}

	protected function presetDefinition()
	{
		return array(
			'vars.preset' => "Hardcore",

			//'vars.serverName' => "LethaK's Frostbite PHP Framework Server",
			//'vars.serverMessage' => '',
			//'vars.serverDescription' => '',
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
			'vars.idleTimeout' => 300,
			'vars.autoBalance' => 1,
			'vars.vehicleSpawnAllowed' => 1,
			'vars.vehicleSpawnDelay' => 100,
			'vars.regenerateHealth' => 0,
			'vars.onlySquadLeaderSpawn' => 1,
			'vars.miniMap' => 0,
			'vars.hud' => 0,
			'vars.3dSpotting' => 0,
			'vars.killCam' => 0,
			'vars.3pCam' => 0,
			'vars.nameTag' => 0,
			'vars.hitIndicatorsEnabled' => 0,
			'vars.playerRespawnTime' => 100,
			'vars.soldierHealth' => 60,
			'vars.forceReloadWholeMags' => 1,
			'vars.roundStartPlayerCount' => 4,
			'vars.commander' => 1, 
			'vars.gameModeCounter' => 100, 
			'vars.roundTimeLimit' => 0,
		);
	}
}

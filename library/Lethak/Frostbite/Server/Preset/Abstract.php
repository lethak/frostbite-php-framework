<?php
# ################################## #
#  LethaK's Frostbite-PHP-Framework  #
# ################################## #
#
# An open-source Framework to interact with Battlefield servers
#
# @author lethak https://github.com/lethak/frostbite-php-framework
#

abstract class Lethak_Frostbite_Server_Preset_Abstract
{
	protected $data;
	private $server;

	abstract protected function label();
	abstract protected function presetDefinition();

	function __construct($extendDefaults=false)
	{
		/*if(!is_array($commandList))
			$commandList = array();*/
		
		if($extendDefaults)
			$this->setData( array_merge($this->defaultDefinition(), $this->presetDefinition()) );
		else
			$this->setData( $this->presetDefinition() );
	}

	protected function setData($data=null)
	{
		if(!is_array($data) && $data!==null)
			$data = array($data);

		$this->data = $data;
	}

	public function toArray()
	{
		return $this->data;
	}

	public function set($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function get($key)
	{
		if(array_key_exists($key, $this->data))
			return $this->data[$key];
		else
			return null;
	}

	public function applyTo(Lethak_Frostbite_Server $server)
	{
		$server->login();
		$vars = $this->toArray();
		$result = array();
		foreach ($vars as $key => $value)
		{
			try
			{
				$result[$key] = $server->rconCommand(array($key, $value));
			}
			catch(Exception $error)
			{
				$result[$key] = array(get_class($error),$error->getMessage());
			}
		}
		return $result;
	}

	protected function defaultDefinition()
	{
		return array(
			'vars.preset' => "Normal",
			
			//'vars.serverName' => "LethaK's Frostbite PHP Framework Server",
			//'vars.serverMessage' => '',
			//'vars.serverDescription' => '',
			//'vars.gamePassword' => '', // Disallowed on Ranked, ReadOnly After Startup
			//'vars.serverType' => 'Official', // Official, Ranked, Unranked, Private

			'vars.friendlyFire' => 0,
			//'vars.teamKillCountForKick' => 5, // Disallowed on Ranked
			//'vars.teamKillKickForBan' => 3,
			//'vars.teamKillValueForKick' => 60*10,
			//'vars.teamKillValueIncrease' => 60,
			//'vars.teamKillValueDecreasePerSecond' => 1,

			# Set idle timeout
			'vars.idleTimeout' => 300,

			# Set whether spectators need to be in the spectator list before joining // CommandIsReadOnly
			//'vars.alwaysAllowSpectators' => 1,

			# Set if the server should autobalance
			'vars.autoBalance' => 1,

			# Set whether vehicles should spawn in-game
			'vars.vehicleSpawnAllowed' => 1,

			# Set vehicle spawn delay scale factor
			'vars.vehicleSpawnDelay' => 100,

			# Set if health regeneration should be active
			'vars.regenerateHealth' => 1,

			# Set if players can only spawn on their squad leader
			'vars.onlySquadLeaderSpawn' => 0,

			# Set if minimap is enabled
			'vars.miniMap' => 1,

			# Set if HUD is enabled
			'vars.hud' => 1,

			# Set if spotted targets are visible on the minimap // CommandIsReadOnly
			//'vars.miniMapSpotting' => 1,

			# Set if spotted targets are visible in the 3d-world
			'vars.3dSpotting' => 1,

			# Set if killcam is enabled
			'vars.killCam' => 1,

			# Set if allowing to toggle to third person vehicle cameras
			'vars.3pCam' => 1,

			# Set if nametags should be displayed
			'vars.nameTag' => 1,

			# Set if hit indicators are enabled or not
			'vars.hitIndicatorsEnabled' => 1,

			# Set player respawn time scale factor
			'vars.playerRespawnTime' => 100,

			# playerManDownTime: Undocumented in BF4 // CommandIsReadOnly
			//'vars.playerManDownTime' => 100, 

			# Set soldier max health scale factor
			'vars.soldierHealth' => 100,

			# Set bullet damage scale factor // CommandIsReadOnly
			//'vars.bulletDamage' => 100,
			
			# Set if crosshair for all weapons is enabled // UnknownCommand
			//'vars.crossHair' => 1,

			# Set hardcore reload on or off
			'vars.forceReloadWholeMags' => 0,

			# Set minimum numbers of players to go from warm-up to pre-round/in-round
			'vars.roundStartPlayerCount' => 4,

			# Set if commander is allowed or not on the game server
			'vars.commander' => 1, 

			# Set scale factor for number of tickets to end round, in percent
			'vars.gameModeCounter' => 100, 

			# Set percentage of the default time limit value, (0 = no limit)
			'vars.roundTimeLimit' => 0,
		);
	}

}
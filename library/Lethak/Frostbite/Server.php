<?php

require_once(dirname(__FILE__).'/Rcon/Exception.php');
require_once(dirname(__FILE__).'/Rcon/Connection.php');

require_once(dirname(__FILE__).'/Player.php');

require_once(dirname(__FILE__).'/Server/Players.php');
require_once(dirname(__FILE__).'/Server/Maps.php');


class Lethak_Frostbite_Server extends Lethak_Frostbite_Rcon_Connection
{
	public $label;
	public $players;
	public $maps;
	

	function __construct($serverIp, $rconPort, $rconPassword=null, $label=null)
	{
		parent::__construct($serverIp, $rconPort, $rconPassword);

		$this->label = $label;
		if($this->label===null||$this->label=="")
			$this->label = "".$this->serverIp.":".$this->rconPort;

		$this->players = new Lethak_Frostbite_Server_Players($this);
		$this->maps = new Lethak_Frostbite_Server_Maps($this);
	}

	public function login($password=null)
	{
		if($password===null||$password=='')
			$password = $this->rconPassword;

		if($password===null||$password==''||$this->isAuthed)
			return $this;

		$this->connectionEnforcement();

		$response = $this->rconCommand('login.hashed');
		if($response[0] != 'OK')
		{
			throw new Lethak_Frostbite_Rcon_Connection_Exception("Could not login with server ".$this->serverIp."");
		}
		$response = $this->rconCommand('login.hashed '.self::generatePasswordHash($response[1], $password));
		
		if($response[0]!='OK')
			$this->isAuthed = false;
		else
			$this->isAuthed = true;
		
		return $this;
	}


	public function getVar($varName=null)
	{
		//ex: $varName='maxPlayers'

		if($varName===null||trim(''.$varName)=="")
			throw new Lethak_Frostbite_Server_Vars_Exception("Unspecified varName");

		$varName = str_replace(' ', '', trim(''.$varName));
		$response = $this->rconCommand('vars.'.$varName);
		if($response[0]!='OK')
			throw new Lethak_Frostbite_Server_Vars_Exception("[vars.".$varName."] ".$response[0]."");

		return array('vars.'.$varName=>intval($response[1]));
	}

	public function apply($whatToApply)
	{
		if($whatToApply instanceof Lethak_Frostbite_Server_Preset_Abstract)
			return $whatToApply->applyTo($this);

		return $this;
	}


	public function say($message='', $playerSubset='all')
	{
		$message = trim((string)$message);
		$message = str_replace(array('"',"'"), '`', $message);
		if($message!=='')
		{
			$cmd = array_merge(array('admin.say', $message), explode(' ', $playerSubset));
			$response = $this->rconCommand($cmd);
		}
		return $this;
	}

	public function yell($message='', $duration=5, $playerSubset='all')
	{
		$message = trim((string)$message);
		$message = str_replace(array('"',"'"), '`', $message);
		if($message!=='')
		{
			$cmd = array_merge(array('admin.yell', $message, $duration), explode(' ', $playerSubset));
			$response = $this->rconCommand($cmd);
		}
		return $this;
	}

	# admin.kickPlayer <player name> <reason>
	# Kick player <soldier name> from server
	public function kick($playerName='', $reason='')
	{
		$playerName = str_replace(' ', '', $playerName);
		if ($playerName!='')
		{
			$cmd = array('admin.kickPlayer', $playerName, $reason);
			$response = $this->rconCommand($cmd);
		}
		return $this;
	}


}


class Lethak_Frostbite_Server_Exception extends Exception {}


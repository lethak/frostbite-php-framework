<?php

require_once(__DIR__.'/Server/Players.php');
require_once(__DIR__.'/Server/Maps.php');
require_once(__DIR__.'/Rcon/Connection.php');

class Lethak_Frostbite_Server extends Lethak_Frostbite_Rcon_Connection
{
	public $label;
	public $players;
	public $maps;

	function __construct($serverIp, $rconPort, $label=null)
	{
		parent::__construct($serverIp, $rconPort);

		$this->label = $label;
		if($this->label===null||$this->label=="")
			$this->label = "".$this->serverIp.":".$this->rconPort;
		
		$this->players = new Lethak_Frostbite_Server_Players($this);
		$this->maps = new Lethak_Frostbite_Server_Maps($this);
	}

	public function login($password)
	{
		$this->connectionEnforcement();

		$response = $this->rconCommand('login.hashed');
		if($response[0] != 'OK')
		{
			throw new Lethak_Frostbite_Rcon_Connection_Exception("Could not login with server ".$this->serverIp."");
		}
		
		$response = $this->rconCommand('login.hashed '.self::generatePasswordHash($response[1], $password));
		switch ($response[0])
		{
			case 'OK':
				//DNT
			break;

			case 'InvalidArguments':
			case 'PasswordNotSet':
			case 'InvalidPasswordHash':
			default:
				throw new Lethak_Frostbite_Rcon_Exception(trim("".$response[0]));
			break;
		}
		
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





}


class Lethak_Frostbite_Server_Exception extends Exception {}
class Lethak_Frostbite_Server_Vars_Exception extends Exception {}
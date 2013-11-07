<?php

require_once(__DIR__.'/Rcon/Connection.php');

class Lethak_Frostbite_Server extends Lethak_Frostbite_Rcon_Connection
{
	public $label;

	function __construct($serverIp, $rconPort, $label=null)
	{
		parent::__construct($serverIp, $rconPort);

		$this->label = $label;
		if($this->label===null||$this->label=="")
			$this->label = "".$this->serverIp.":".$this->rconPort;
	}

	public function login($password)
	{
		$this->connectionEnforcement();

		$response = $this->rconCommand('login.hashed');
		if($response[0] != 'OK')
		{
			$this->connectionClose();
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
				$this->connectionClose();
				throw new Lethak_Frostbite_Rcon_Exception(trim("".$response[0]));
			break;
		}
		
		return $this;
	}

}

?>
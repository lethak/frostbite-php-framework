<?php

require_once(__DIR__.'/Protocol.php');

class Lethak_Frostbite_Rcon_Connection extends Lethak_Frostbite_Rcon_Protocol
{
	public $serverIp;
	public $rconPort;

	function __construct($serverIp, $rconPort)
	{
		parent::__construct();

		$this->serverIp = $serverIp;
		$this->rconPort = $rconPort;
	}

	function __destruct()
	{
		$this->connectionClose();
	}

	public function connect()
	{
		$this->clientSequenceNr = 0;
		try
		{
			$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			socket_connect($this->socket, $this->serverIp, $this->rconPort);
			socket_set_nonblock($this->socket);
			if($this->socket === false)
			{
				throw new Lethak_Frostbite_Rcon_Connection_Exception
					("Failed to establish a socket connection with server ".$this->serverIp.":".$this->rconPort."");
			}
		}
		catch(Lethak_Frostbite_Rcon_Protocol_Exception $Error)
		{
			throw $Error;
		}
		catch(Lethak_Frostbite_Rcon_Connection_Exception $Error)
		{
			throw $Error;
		}
		catch(Exception $Error)
		{
			throw new Lethak_Frostbite_Rcon_Connection_Exception($Error->getmessage());
		}
		
		return $this;
	}

	protected function connectionEnforcement()
	{
		if ($this->socket===false)
			$this->connect();
		return $this;
	}



}

class Lethak_Frostbite_Rcon_Exception extends Exception {}
class Lethak_Frostbite_Rcon_Connection_Exception extends Exception {}

?>
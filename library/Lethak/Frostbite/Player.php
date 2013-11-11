<?php

class Lethak_Frostbite_Player
{

	/*
        [name] => lethak
        [guid] => EA_5FF93997EB97B108B643F1513E0F4XXX
        [teamId] => 1
        [squadId] => 1
        [kills] => 0
        [deaths] => 0
        [score] => 0
        [rank] => 36
        [ping] => 27
	*/


	protected $data;
	private $server;

	function __construct($data=null, Lethak_Frostbite_Server &$Server)
	{
		$this->server = $Server;
		$this->setData($data);
	}

	public function setData($data=null)
	{
		if(!is_array($data) && $data!==null)
			$data = array($data);

		$this->data = $data;
	}

	public function setVar($key, $value)
	{
		
	}

	public function toArray()
	{
		return $this->data;
	}

	function __get($key)
	{
		if(array_key_exists($key, $this->data))
			return $this->data[$key];
		else
			return null;
	}

	function __set($key, $value)
	{
		$this->data[$key] = $value;
	}


	## -------------------------------------------------------------------------


	public function isSpectator()
	{
		throw new Exception('NotImplementedYet');
		
	}

	# admin.movePlayer <name> <teamId> <squadId> <forceKill>
	# Move a player to another team and squad
	public function move()
	{
		throw new Exception('NotImplementedYet');
	}

	# admin.killPlayer <player name>
	# Kill a player without scoring effects
	public function kill()
	{
		throw new Exception('NotImplementedYet');
	}

	# admin.kickPlayer <player name> <reason>
	# Kick player <soldier name> from server
	public function kick()
	{
		throw new Exception('NotImplementedYet');
	}

	public function ban()
	{
		throw new Exception('NotImplementedYet');
	}

	public function say($message='')
	{
		$this->server->say($message, '"player" '.$this->data['name'].'"');
	}

	public function yell($message='', $duration=5)
	{
		$this->server->yell($message, $duration, '"player" '.$this->data['name'].'"');
	}

	public function idleDuration()
	{
		throw new Exception('NotImplementedYet');
	}

	# player.isAlive <playername>
	# Check if the soldier is alive
	public function isAlive()
	{
		throw new Exception('NotImplementedYet');
	}

	# player.ping <playername>
	# Get a soldiers ping to the server
	public function ping()
	{
		throw new Exception('NotImplementedYet');
	}


}

class Lethak_Frostbite_Player_Exception extends Exception {}


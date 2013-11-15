<?php
# ################################## #
#  LethaK's Frostbite-PHP-Framework  #
# ################################## #
#
# An open-source Framework to interact with Battlefield servers
#
# @author lethak https://github.com/lethak/frostbite-php-framework
#


/**
* This class is used internaly to handle the connection stuff between Frostbite and php
* 
* @author RobFreiburger https://github.com/RobFreiburger
* @author lethak https://github.com/lethak/
*/
class Lethak_Frostbite_Rcon_Connection
{
	public $serverIp;
	public $rconPort;
	public $rconPassword;

	public $isDebugProtocol;
	public $streamTimeout;

	protected $socket;
	protected $clientSequenceNr;
	protected $isAuthed;

	private $receiveBuffer;

	function __construct($serverIp, $rconPort, $rconPassword)
	{

		$this->serverIp = $serverIp;
		$this->rconPort = $rconPort;

		$this->isDebugProtocol=false;
		$this->socket = false;
		$this->clientSequenceNr = 0;
		$this->receiveBuffer = '';
		$this->streamTimeout = 5;
		$this->rconPassword = ''.$rconPassword;
		$this->isAuthed = false;
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

			socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array('sec'=>$this->streamTimeout, 'usec'=>0));
			socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, array('sec'=>$this->streamTimeout, 'usec'=>0));

			$isSuccess = @socket_connect($this->socket, $this->serverIp, $this->rconPort);
			if($isSuccess===false)
				$this->socket=false;

			if($this->socket === false)
			{
				throw new Lethak_Frostbite_Rcon_Connection_Exception
					("Failed to establish a socket connection with server ".$this->serverIp.":".$this->rconPort."");
			}
			else{
				socket_set_nonblock($this->socket);
			}
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

	public function connectionEnforcement()
	{
		if ($this->socket===false)
			$this->connect();
		return $this;
	}

	static function EncodeInt32($size)
	{
		return pack('I', $size);
	}

	static function DecodeInt32($data)
	{
		$decode = unpack('I', mb_substr($data, 0, 4));
		return $decode[1];
	}

	static function DecodeHeader($data)
	{
		$header = unpack('I', mb_substr($data, 0, 4));
		return array($header & 0x80000000, $header & 0x40000000, $header & 0x3fffffff);
	}

	static function EncodeHeader($isFromServer, $isResponse, $sequence)
	{
		$header = $sequence & 0x3fffffff;
		if ($isFromServer)
		{
			$header += 0x80000000;
		}
		if ($isResponse)
		{
			$header += 0x40000000;
		}
		return pack('I', $header);
	}

	static function EncodeWords($words)
	{
		$size = 0;
		$encodedWords = '';
		foreach ($words as $word)
		{
			$encodedWords .= self::EncodeInt32(strlen($word));
			$encodedWords .= $word;
			$encodedWords .= "\x00";
			$size += strlen($word) + 5;
		}
		return array($size, $encodedWords);
	}

	static function DecodeWords($size, $data)
	{
		$numWords = self::DecodeInt32($data);
		$words = array();
		$offset = 0;
		while ($offset < $size)
		{
			$wordLen = self::DecodeInt32(mb_substr($data, $offset, 4));
			$word = mb_substr($data, $offset + 4, $wordLen);
			array_push($words, $word);
			$offset += $wordLen + 5;
		}
		return $words;
	}

	function EncodePacket($isFromServer, $isResponse, $sequence, $words)
	{
		$encodedHeader = self::EncodeHeader($isFromServer, $isResponse, $sequence);
		$encodedNumWords = self::EncodeInt32(count($words));
		list($wordsSize, $encodedWords) = self::EncodeWords($words);
		$encodedSize = self::EncodeInt32($wordsSize + 12);
		
		return $encodedHeader . $encodedSize . $encodedNumWords . $encodedWords;
	}

	static function DecodePacket($data)
	{
		list($isFromServer, $isResponse, $sequence) = self::DecodeHeader($data);
		$wordsSize = self::DecodeInt32(mb_substr($data, 4, 4)) - 12;
		$words = self::DecodeWords($wordsSize, mb_substr($data, 12));
		return array($isFromServer, $isResponse, $sequence, $words);
	}


	static function EncodeClientResponse($sequence, $words)
	{
		return self::EncodePacket(true, true, $sequence, $words);
	}

	static function containsCompletePacket($data)
	{
		if (mb_strlen($data) < 8)
			return false;

		if (mb_strlen($data) < self::DecodeInt32(mb_substr($data, 4, 4)))
			return false;

		return true;
	}

	static function printPacket($packet)
	{
		if ($packet[0])
			echo "IsFromServer, $packet[0] ";
		else
			echo "IsFromClient, ";
		
		if ($packet[1])
			echo "Response, $packet[1] ";
		else
			echo "Request, ";
		
		echo "Sequence: $packet[2]";
		
		if ($packet[3])
		{
			echo " Words:";
			foreach ($packet[3] as $word)
			{
				echo " \"$word\"";
			}
		}
	}

	// Hashed password helper functions
	static function hexstr($hexstr)
	{
		$hexstr = str_replace(' ', '', $hexstr);
		$hexstr = str_replace('\x', '', $hexstr);
		$retstr = pack('H*', $hexstr);
		return $retstr;
	}

	static function strhex($string)
	{
		$hexstr = unpack('H*', $string);
		return array_shift($hexstr);
	}

	static function generatePasswordHash($salt, $password)
	{
		$salt = self::hexstr($salt);
		$hashedPassword = md5($salt . $password, true);
		return strtoupper(self::strhex($hashedPassword));
	}

	/**
	 * Main RCON Command. Call this to send a command to the server.
	 * Arguments: socket, command to send
	 *
	 * @return array of words; server's response
	 **/
	public function rconCommand($string, $iteration=0)
	{
		
		if($iteration<0)
			$iteration = 0;
		if($iteration>2)
			throw new Lethak_Frostbite_Rcon_Command_Exception('Too much iteration !');

		$this->connectionEnforcement();
		if (socket_write($this->socket, $this->EncodeClientRequest($string)) === false)
		{
			$this->connectionClose();
			throw new Lethak_Frostbite_Rcon_Connection_Exception("Socket error: " . socket_strerror(socket_last_error($this->socket)));
		}
		
		list($packet, $receiveBuffer) = $this->receivePacket($this->socket);
		if($this->isDebugProtocol){
			self::printPacket(self::DecodePacket($packet));
		}
		list($isFromServer, $isResponse, $sequence, $words) = self::DecodePacket($packet);

		if($this->throwExceptions($words[0], $string))
			$words = $this->rconCommand($string, $iteration++);

		return $words;
	}

	private function throwExceptions($responseCode, $cmd)
	{
		if(substr($responseCode, 0,2)!='OK')
		{
			if (is_array($cmd))
				$cmd2 = implode(' ', $cmd);
			else
				$cmd2 = $cmd;

			switch($responseCode)
			{
				case 'LogInRequired':
					if(''.$this->rconPassword!='')
					{
						$L = $this->login();
						return true;
					}
					else
						throw new Lethak_Frostbite_Rcon_LogInRequired_Exception($cmd2);
				break;

				case 'InvalidArgument':
				case 'InvalidArguments':
					throw new Lethak_Frostbite_Rcon_InvalidArguments_Exception($cmd2);
				break;

				case 'InvalidNumberOfArguments':
					throw new Lethak_Frostbite_Rcon_InvalidNumberOfArguments_Exception($cmd2);
				break;

				case 'PasswordNotSet':
					throw new Lethak_Frostbite_Rcon_PasswordNotSet_Exception($cmd2);
				break;

				case 'InvalidPassword':
				case 'InvalidPasswordHash':
					throw new Lethak_Frostbite_Rcon_InvalidPassword_Exception($cmd2);
				break;

				case 'PlayerNotFound':
					throw new Lethak_Frostbite_Rcon_PlayerNotFound_Exception($cmd2);
				break;

				case 'MessageIsTooLong':
				case 'TooLongMessage':
					throw new Lethak_Frostbite_Rcon_MessageIsTooLong_Exception($cmd2);
				break;

				case 'InvalidPlayer':
				case 'InvalidPlayerId':
				case 'InvalidPlayerName':
					throw new Lethak_Frostbite_Rcon_InvalidPlayer_Exception($cmd2);
				break;

				case 'SoldierNotDead':
				case 'PlayerNotDead':
					throw new Lethak_Frostbite_Rcon_PlayerNotDead_Exception($cmd2);
				break;

				case 'SoldierNotAlive':
				case 'PlayerNotAlive':
					throw new Lethak_Frostbite_Rcon_SoldierNotAlive_Exception($cmd2);
				break;

				case 'InvalidForceKill':
					throw new Lethak_Frostbite_Rcon_InvalidForceKill_Exception($cmd2);
				break;
				
				case 'InvalidSquad':
				case 'InvalidSquadId':
					throw new Lethak_Frostbite_Rcon_InvalidSquad_Exception($cmd2);
				break;

				case 'InvalidTeam':
				case 'InvalidTeamId':
					throw new Lethak_Frostbite_Rcon_InvalidTeam_Exception($cmd2);
				break;

				case 'CommandIsReadOnly':
					throw new Lethak_Frostbite_Rcon_CommandIsReadOnly_Exception($cmd2);
				break;
				
				case 'UnknownCommand':
					throw new Lethak_Frostbite_Rcon_UnknownCommand_Exception($cmd2);
				break;
				
				case 'SetTeamFailed':
				case 'SetSquadFailed':
				case 'BanListFull':
				case 'InvalidIdType':
				case 'InvalidBanType':
				case 'InvalidTimeStamp':
				case 'IncompleteBan':
				case 'AccessError':
				default:
					throw new Lethak_Frostbite_Rcon_Exception($responseCode);
				break;
			}
		}

		return false;
	}

	// Untested yet
	public function rconCommandBatch($arrayOfRconCommand=array())
	{
		if(!is_array($arrayOfRconCommand))
			$arrayOfRconCommand = array();

		$result = array();
		foreach ($arrayOfRconCommand as $key => $cmd)
		{
			try
			{
				$result[$key] = $server->rconCommand($cmd);
			}
			catch(Exception $error)
			{
				$result[$key] = $error;
			}
		}
		return $result;
	}

	private function receivePacket()
	{
		$this->receiveBuffer = '';	
		while (!self::containsCompletePacket($this->receiveBuffer))
		{
			
			if (($this->receiveBuffer .= socket_read($this->socket, 4096)) === false)
			{
				socket_close($this->socket);
				throw new Lethak_Frostbite_Rcon_Protocol_Exception("".socket_strerror(socket_last_error($this->socket)));
			}
		}
		$packetSize = self::DecodeInt32(mb_substr($this->receiveBuffer, 4, 4));
		$packet = mb_substr($this->receiveBuffer, 0, $packetSize);
		$receiveBuffer = mb_substr($this->receiveBuffer, $packetSize);
		return array($packet, $this->receiveBuffer);
	}

	private function EncodeClientRequest($rconCommand)
	{
		if(is_array($rconCommand))
		{
			$words = $rconCommand;
		}
		else
		{
			$string = $rconCommand;
			$splited = false;
			// string splitting
			if ((strpos($string, '"') !== false) or (strpos($string, '\'') !== false)) {
				$words = preg_split('/["\']/', $string);

				for ($i=0; $i < count($words); $i++) { 
					$words[$i] = trim($words[$i]);
				}
				$splited = true;
			} else {
				$words = preg_split('/\s+/', $string);
			}
		}

		foreach ($words as $key => $value)
		{
			if($value===null||$value=='')
				unset($words[$key]);
		}
		
		$packet = self::EncodePacket(false, false, $this->clientSequenceNr, $words);
		
		//if($splited) die('<pre>'.print_r($words, true).'</pre>');

		$this->clientSequenceNr = ($this->clientSequenceNr + 1) & 0x3fffffff;

		return $packet;
	}

	public function connectionClose()
	{
		@socket_close($this->socket);
		$this->socket = false;
		return $this;
	}


}


class Lethak_Frostbite_Rcon_Connection_Exception extends Exception {}
class Lethak_Frostbite_Rcon_Protocol_Exception extends Exception {}
class Lethak_Frostbite_Rcon_Command_Exception extends Exception {}

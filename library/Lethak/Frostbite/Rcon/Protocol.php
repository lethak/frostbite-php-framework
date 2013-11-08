<?php
/**
* @thanks https://github.com/RobFreiburger/Battlefield-3-RCON-PHP-Scripts/blob/master/rcon.funcs.php
*/
class Lethak_Frostbite_Rcon_Protocol
{
	public $isDebugProtocol;
	protected $socket;
	protected $clientSequenceNr;
	private $receiveBuffer;

	function __construct()
	{
		$this->isDebugProtocol=false;
		$this->socket = false;
		$this->clientSequenceNr = 0;
		$this->receiveBuffer = '';
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
	public function rconCommand($string)
	{
		
		if (socket_write($this->socket, $this->EncodeClientRequest($string)) === false)
		{
			$this->connectionClose();
			throw new Lethak_Frostbite_Rcon_Protocol_Exception("Socket error: " . socket_strerror(socket_last_error($this->socket)));
		}
		
		list($packet, $receiveBuffer) = $this->receivePacket($this->socket);
		if($this->isDebugProtocol){
			self::printPacket(self::DecodePacket($packet));
		}
		list($isFromServer, $isResponse, $sequence, $words) = self::DecodePacket($packet);

		return $words;
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

	private function EncodeClientRequest($string)
	{
		// string splitting
		if ((strpos($string, '"') !== false) or (strpos($string, '\'') !== false)) {
			$words = preg_split('/["\']/', $string);

			for ($i=0; $i < count($words); $i++) { 
				$words[$i] = trim($words[$i]);
			}
		} else {
			$words = preg_split('/\s+/', $string);
		}
		
		$packet = self::EncodePacket(false, false, $this->clientSequenceNr, $words);
		$this->clientSequenceNr = ($this->clientSequenceNr + 1) & 0x3fffffff;

		return $packet;
	}

	public function connectionClose()
	{
		//$this->rconCommand('quit');
		@socket_close($this->socket);
		$this->socket = false;
		return $this;
	}


}




class Lethak_Frostbite_Rcon_Protocol_Exception extends Exception {}


<?php
# #################################### #
#   LethaK's Frostbite-PHP-Framework   #
# #################################### #
#
# An open-source Framework to interact with Battlefield servers
#
# @author lethak https://github.com/lethak/frostbite-php-framework
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
#
# # # # # # # # # # # # # # # # # # # #


require_once(dirname(__FILE__).'/Rcon/Exception.php');
require_once(dirname(__FILE__).'/Rcon/Connection.php');

require_once(dirname(__FILE__).'/Player.php');

require_once(dirname(__FILE__).'/Server/Players.php');
require_once(dirname(__FILE__).'/Server/Maps.php');

/**
 * Use this class as an entry point to connect and interact with your gameserver and its attributes.
 * 
 * @author lethak
 */
class Lethak_Frostbite_Server extends Lethak_Frostbite_Rcon_Connection
{

	/**
	 * @var string
	 */
	public $label;

	/**
	 * @var Lethak_Frostbite_Server_Players
	 */
	protected $players;

	/**
	 * @var Lethak_Frostbite_Server_Maps
	 */
	protected $maps;
	
	/**
	 * 
	 * @param string $serverIp IP address used to connect to the game-server's remote admin protocol
	 * @param integer $rconPort Remote Admin Protocol connection port (often 47200)
	 * @param string $rconPassword (Optional) This must be the rcon password of the server. Used to auto-auth if provided
	 * @param string $label (Optional) label to be fetched later in your application.
	 */
	function __construct($serverIp, $rconPort=47200, $rconPassword=null, $label=null)
	{
		parent::__construct($serverIp, $rconPort, $rconPassword);

		$this->label = $label;
		if($this->label===null||$this->label=="")
			$this->label = "".$this->serverIp.":".$this->rconPort;

		$this->players = new Lethak_Frostbite_Server_Players($this);
		$this->maps = new Lethak_Frostbite_Server_Maps($this);
	}

	function __get($key)
	{
		if(property_exists('Lethak_Frostbite_Server', $key))
			return $this->$key;
		return null;
	}

	function __set($key, $value)
	{
		//DNT
	}

	/**
	 * Used to authenticate with the Remote Admin Server
	 * 
	 * The rcon password must be set serer-side,
	 * and provided to the method or the server object (preferably)
	 * @param string $password This must be the rcon password of the server (Optional if provided with the constructor)
	 * @throws Lethak_Frostbite_Rcon_Connection_Exception
	 * @throws Exception
	 * @return Lethak_Frostbite_Server
	 */
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


	/**
	 * getVar
	 * @deprecated I will remove this method to replace it with a more robust and generic
	 * @return array
	 */
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

	/**
	 * This method is a shortcut aiming to apply 'something' to the server.
	 * At the moment, only Lethak_Frostbite_Server_Preset_Abstract are implemented
	 *
	 * @throws Exception
	 * @return Lethak_Frostbite_Server
	 */
	public function apply($whatToApply)
	{
		if($whatToApply instanceof Lethak_Frostbite_Server_Preset_Abstract)
			return $whatToApply->applyTo($this);

		return $this;
	}

	
	/**
	 * (shortcut) Sending a chat message to all players
	 *
	 * Displayed within the ingame chat window, prefixed by '[ADMIN]'
	 * Using rcon command: admin.say message all
	 *
	 * @param string $message
	 * @throws Exception
	 * @return Lethak_Frostbite_Server
	 */
	public function say($message='')
	{
		$this->players->say($message, 'all');
		return $this;
	}

	/**
	 * (shortcut) Sending a yell message to all players
	 *
	 * Displayed in front of the game-client screen for a specified duration
	 * Using rcon command: admin.yell
	 *
	 * @param string $message
	 * @param string $duration
	 * @throws Exception
	 * @return Lethak_Frostbite_Server_Players
	 */
	public function yell($message='', $duration=5)
	{
		$this->players->yell($message, $duration, 'all');
		return $this;
	}

}


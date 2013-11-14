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


/**
 * This class is used to map a player entity
 * @author lethak
 */
class Lethak_Frostbite_Player
{

	/**
	 * Array container
	 *
     *   [name] => lethak
     *   [guid] => EA_5FF93997EB97B108B643F1513E0F4XXX
     *   [teamId] => 1
     *   [squadId] => 1
     *   [kills] => 0
     *   [deaths] => 0
     *   [score] => 0
     *   [rank] => 36
     *   [ping] => 27
     * 
     * @var array
	 */
	protected $data;

	/**
	 * @var Lethak_Frostbite_Server
	 */
	private $server;

	function __construct($data=null, Lethak_Frostbite_Server &$Server)
	{
		$this->server = $Server;
		$this->setData($data);
	}

	/**
	 * This will erase the current Player object and populate it with your $data
	 * @param array $data
	 * @return Lethak_Frostbite_Player
	 */
	public function setData($data=null)
	{
		if(!is_array($data) && $data!==null)
			$data = array($data);

		$this->data = $data;
		return $this;
	}

	/**
	 * This function returns an array with player infos
	 * @return array
	 */
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



	# admin.movePlayer <name> <teamId> <squadId> <forceKill>
	# Move a player to another team and squad
	public function move()
	{
		throw new Exception('NotImplementedYet');
	}

	/**
	 * Kill a player without scoring effects
	 *
	 * Using rcon command: admin.killPlayer
	 *
	 * @throws Exception
	 * @return Lethak_Frostbite_Player
	 */
	public function kill()
	{
		$this->server->players->killPlayer($this->name);
		return $this;
	}

	/**
	 * Kick a player from the server
	 *
	 * Using rcon command: admin.kickPlayer
	 *
	 * @param string $reason (Optional) Displayed to the user as the reason why they were kicked
	 * @throws Exception
	 * @return Lethak_Frostbite_Player
	 */
	public function kick($reason='')
	{
		$this->server->players->kick($this->name, $reason);
		return $this;
	}

	public function ban()
	{
		throw new Exception('NotImplementedYet');
		return $this;
	}

	public function say($message='')
	{
		$this->server->players->say($message, 'player '.$this->name);
		return $this;
	}

	public function yell($message='', $duration=5)
	{
		$this->server->players->yell($message, $duration, 'player '.$this->name);
		return $this;
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

	public function isSpectator()
	{
		throw new Exception('NotImplementedYet');
	}

}

class Lethak_Frostbite_Player_Exception extends Exception {}


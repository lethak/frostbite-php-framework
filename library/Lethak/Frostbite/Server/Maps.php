<?php
# ################################## #
#  LethaK's Frostbite-PHP-Framework  #
# ################################## #
#
# An open-source Framework to interact with Battlefield servers
#
# @author lethak https://github.com/lethak/frostbite-php-framework
#

class Lethak_Frostbite_Server_Maps
{
	private $server;
	function __construct(Lethak_Frostbite_Server &$Server)
	{
		$this->server = $Server;
	}

}
<?php
# ################################## #
#  LethaK's Frostbite-PHP-Framework  #
# ################################## #
#
# An open-source Framework to interact with Battlefield servers
#
# @author lethak https://github.com/lethak/frostbite-php-framework
#

require_once(dirname(__FILE__).'/Abstract.php');

class Lethak_Frostbite_Server_Preset_Custom extends Lethak_Frostbite_Server_Preset_Abstract
{

	protected $definition;
	protected $presetLabel;

	public function __construct($definitionArray, $presetLabel='Custom Preset', $extendDefaults=false)
	{
		if(!is_array($definitionArray))
			$definitionArray = array();

		$this->definition = $definitionArray;
		$this->presetLabel = $presetLabel;
		parent::__construct($extendDefaults);
	}

	public function label()
	{
		return $this->presetLabel;
	}

	protected function presetDefinition()
	{
		return $this->definition;
	}
}
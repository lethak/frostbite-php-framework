LethaK's Frostbite-PHP-Framework
=======================

Still under development.


Features
--------

* A lightweight PHP Framework library wrapping Frostbite's RCON protocol.
* Lets you trigger admin command from your website or CLI.
* Tested with Battlefield 4 server R6 and R7.
* Error handling via Exception
* Object Oriented from the user perspective

```php
$server = new Lethak_Frostbite_Server("192.168.0.1", 47200, 'myRconPassword');
$server
	->login()
	->yell('Hello World !')
	->rconCommand('vars.friendlyFire 1')
;
// ...
$player->say('Goodbye '.$player->name.' !')->kick();
```


* Zend Framework's 1.x Class autoloader compatible

```php
# library/LethaK/Frostbite/Server.php == class Lethak_ForstBite_Server
# library/LethaK/Frostbite/Player.php == class Lethak_ForstBite_Player
```


What is working ?
-----------------
- Quick preset switching (Normal, Hardcore, Inf)
- You can create your own preset
- You can define a dynamically generated preset (on the fly)
- Issuing a single or bulk rcon commands (request/response)
- Server login and auth process (hashed)
- toying with players (list/kill/kick/say/yell)
- server say/yell


What will be working soon ?
---------------------------
- player.move (squad and team)
- Maplist (roundRestart, etc)


What is planned ahead ?
-----------------------
- BanList
- ReservedSlotList
- SpectatorList
- All the other stuff available within the Remote Admin Protocol of frostbite 2, no timetable.


Example: RCON Login
-------------------

See more: https://github.com/lethak/frostbite-php-framework/tree/master/examples/server-login.php

Authenticate as the server administrator using the RCON password to be granted access to restricted commands

```php
$server = new Lethak_Frostbite_Server("127.0.0.1", 47200);
$server->login("myRconPassword");

// Fetch the player list,
// it will internaly use admin.playerList if authed, playerList if not authed...
$playerList = $server->players->getList();

// HTML formated rendering of the array list...
echo '<pre>'.print_r($playerList,1).'</pre>'; 

```


Example: Multiple server instance
---------------------------------


Because one does not simply have a single server to manage.


```php
// Server list declaration
$serverList[] = new Lethak_Frostbite_Server("127.0.0.1", 47200, "myRconPassword1");
$serverList[] = new Lethak_Frostbite_Server("127.0.0.2", 47200, "myRconPassword2");
$serverList[] = new Lethak_Frostbite_Server("127.0.0.3", 47200);

// Issuing commands quickly to the server list
foreach($serverList as $server)
{
  // do your stuff ...
  $some_stuff = $server->getVar('maxPlayers');
  $server->say('Team killing for vehicles is prohibited');
}
```

Example: Error handling
---------------------------------

See more: https://github.com/lethak/frostbite-php-framework/tree/master/examples/server-login.php


```php
try
{
  $server = new Lethak_Frostbite_Server("127.0.0.1", 47200);
  $some_stuff = $server->rconCommand('this.is-not-a-valid-command');
}
catch(Exception $error)
{
  echo('<pre>'.print_r($error,1).'</pre>');
}

```



Example: Creating you own preset class
----------------------------------------

See more: https://github.com/lethak/frostbite-php-framework/tree/master/examples/server-preset.php

For the moment, you have to create your own class like this one.
Remember to have the class Lethak_Frostbite_Server_Preset_Abstract included.

```php
class myFastVehiclePreset extends Lethak_Frostbite_Server_Preset_Abstract
{

	public function label()
	{
		return 'Custom Fast Vehicle Preset';
	}

	protected function presetDefinition()
	{
		return array(
			'vars.friendlyFire' => 1,
			'vars.vehicleSpawnAllowed' => 1,
			'vars.vehicleSpawnDelay' => 20,
		);
	}
}

```

Example: Applying a server preset
---------------------------------

See more: https://github.com/lethak/frostbite-php-framework/tree/master/examples/server-preset.php

This example feature 3 possible methods of using a preset.

- It is possible to use a preset embeded with this framework.
- It is possible to use your own preset class (created in the above example)
- It is possible to define a preset on the fly using an array of vars.


```php
try
{
	$server = new Lethak_Frostbite_Server("127.0.0.1", 47200, "myRconPassword");
	

	// Embeded 'Hardcore' Preset
	$hardcorePreset = new Lethak_Frostbite_Server_Preset_Hardcore();

	// Some custom preset, loaded from a database or else
	$customVariables = array(
		'vars.friendlyFire' => 1,
		'vars.killCam' => 0,
	);
	$customPreset = new Lethak_Frostbite_Server_Preset_Custom($customVariables);

	// A custom class preset
	// Remember to have the class myFastVehiclePreset included.
	$myPresetClass = new myFastVehiclePreset();

	// Applying previously defined preset
	$RESULT = array(
		$server->apply($hardcorePreset),
		$server->apply($customPreset),
		$server->apply($myPresetClass),
	);

}
catch(Exception $error)
{
	die('Exception: ['.get_class($error).'] <pre>'.print_r($error->getMessage(), true).'</pre>');
}

echo('FINISHED: <pre>'.print_r($RESULT, true).'</pre>');exit;
```
LethaK's Frostbite-PHP-Framework
=======================

Features
--------

* A lightweight PHP Framework library wrapping Frostbite's RCON protocol.
* Lets you trigger admin command from your website or CLI.
* Tested with Battlefield 4 server R6 and R7.
* Object Oriented from the user perspective

```php
$Server = new Lethak_Frostbite_Server("192.168.0.1", 47200);
$Server->login("myRconPassword");
```


* Zend Framework's 1.x Class autoloader compatible

```php
# library/LethaK/Frostbite/Server.php == class Lethak_ForstBite_Server
# library/LethaK/Frostbite/Player.php == class Lethak_ForstBite_Player
```

RCON Login
-----------

```php
$Server = new Lethak_Frostbite_Server("192.168.0.1", 47200);

// Login with the RCON Password to be granted access to restricted commands...
$Server->login("myRconPassword");

// Fetch the player list,
// it will internaly use admin.playerList if authed, playerList if not...
$playerList = $server->players->list();

// HTML formated rendering of the array list...
echo '<pre>'.print_r($playerList,1).'</pre>'; 

```


Multiple server instance
-----------

```php
// Server list declaration
$serverList[] = new Lethak_Frostbite_Server("192.168.0.1", 47200);
$serverList[] = new Lethak_Frostbite_Server("192.168.0.2", 47200);

// Issuing commands quickly to all servers ...
foreach($serverList as $server)
{
  echo 'This server has '.$server->getVar('maxPlayers').' maxPlayers.';
}
```

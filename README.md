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
$Server = new Lethak_Frostbite_Server("192.168.0.1", 47200);
$Server->connect();
$some_stuff = $Server->getVar('maxPlayers');
```


* Zend Framework's 1.x Class autoloader compatible

```php
# library/LethaK/Frostbite/Server.php == class Lethak_ForstBite_Server
# library/LethaK/Frostbite/Player.php == class Lethak_ForstBite_Player
```



What is working ?
-----------------
- Server connection via sockets
- Issuing rcon commands (request/response)
- Server login auth process (hashed)

What will be working soon ?
---------------------------
- player list when authed
- player list when not authed
- server.say
- server.yell


What is planned ahead ?
----------------------
- Preset definition in order to allow quick preset switching and even your own preset.
- All the other stuff available within the Remote Control Protocol of frostbite 2, no timetable.


Exemple: RCON Login
-----------

Authenticate as the server administrator using the RCON password to be granted access to restricted commands

```php
$Server = new Lethak_Frostbite_Server("192.168.0.1", 47200);
$Server->login("myRconPassword");

// Fetch the player list,
// it will internaly use admin.playerList if authed, playerList if not authed...
$playerList = $server->players->list();

// HTML formated rendering of the array list...
echo '<pre>'.print_r($playerList,1).'</pre>'; 

```


Exemple: Multiple server instance
------------------------

Because one does not simply have a single server to manage.


```php
// Server list declaration
$serverList[] = new Lethak_Frostbite_Server("192.168.0.1", 47200);
$serverList[] = new Lethak_Frostbite_Server("192.168.0.2", 47200);

// Issuing commands quickly to the server list
foreach($serverList as $server)
{
  // do your stuff ...
  $some_stuff = $server->getVar('maxPlayers');
  $server->say('Team killing for vehicles is prohibited');
}
```

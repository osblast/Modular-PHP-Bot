<?php

	include_once("bot.php");
	include_once("botConfig.php");

	$ircServer = new IRCServer("irc.freenode.net", 6667);
	$ircBot = new PHPBot(new botConfig("Franciswa", "Yes, I'm Franciswa!", $ircServer));
	$ircBot->Run();

?>
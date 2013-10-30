<?php

	include_once("src/RunBot.php");
	include_once("DemoBot/DemoBot.php");

	$DemoBot = new DemoBot("osPHPBot", "osBlast PHP Bot", "irc.freenode.net", 6667, "#osBlast");
	$BotRunner = new RunBot($DemoBot);

	$BotRunner->Go();

?>
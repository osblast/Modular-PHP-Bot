<?php

	$version_string = "Another Modular PHP Bot [v".PHP_BOT_VERSION_MAJOR.".".PHP_BOT_VERSION_MINOR.".".PHP_BOT_VERSION_REVISION."] by Anthony Musgrove (anthonym/IRC@freenode) [Build #".PHP_BOT_VERSION_BUILD."]";
	$bot->socketHandler->send($this->ircHandler->NOTICE($from_nick, chr(1)."VERSION ".$version_string.chr(1)));

?>
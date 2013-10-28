<?php

	$time_string = date('Y-m-d H:i:s');

	$bot->socketHandler->send($this->ircHandler->NOTICE($from_nick, chr(1)."TIME ".$time_string.chr(1)));

?>
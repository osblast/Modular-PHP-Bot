<?php
	$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "Why, you're: $from_nick ($from_userid@$from_host)"));
?>
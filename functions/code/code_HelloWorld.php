<?php
	$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "This is a changed function after run time!, This is the hello world function process, it was initiated by $from_nick ($from_userid@$from_host)"));
?>
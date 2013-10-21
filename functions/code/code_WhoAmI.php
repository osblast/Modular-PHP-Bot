<?php
	$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "Why, you're: $from_nick ($from_userid@$from_host)"));
?>
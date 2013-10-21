<?php

	$join_data = array_filter(explode(" ", $data));

	if(count($join_data) < 5)
	{
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "You need to specify a channel name and an optional key.  usage: JOIN <#Channel> [<Key>]"));
	}
	else if(substr($join_data[4],0,1) != "#")
	{
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "Sorry, the channel you specified (".$join_data[4].") is not a valid channel name."));
	}
	else
	{
		$chan_to_join = $join_data[4];

		if($bot->ChanList->findChannelByName($chan_to_join) != null)
		{
			$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "I'm already on channel $chan_to_join, silly!"));
			return;
		}

		if(count($join_data) > 5)
		{
			$channel_key = $join_data[5];
			$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "Joining channel: ".$chan_to_join." at request of ".$from_nick." using channel key: ".$channel_key));
			$bot->socketHandler->send($bot->ircHandler->JOIN($chan_to_join, $channel_key));
		}
		else
		{
			$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "Joining channel: ".$chan_to_join." at request of ".$from_nick));
			$bot->socketHandler->send($bot->ircHandler->JOIN($chan_to_join, ""));
		}

	}


?>


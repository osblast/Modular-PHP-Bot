<?php

$part_data = array_filter(explode(" ", $data));

if(count($part_data) < 5)
{
	$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "You need to specify a channel name and an optional part message.  usage: PART <#Channel> [<Reason>]"));
}
else if(substr($part_data[4],0,1) != "#")
{
	$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "Sorry, the channel you specified (".$part_data[4].") is not a valid channel name."));
}
else
{
	$chan_to_part = $part_data[4];

	if($bot->ChanList->findChannelByName($chan_to_part) == null)
	{
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "I'm not on channel $chan_to_part, I can't part it if I'm not on it!"));
		return;
	}


	if(count($part_data) > 5)
	{
		$part_message = $part_data[5];
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "Parting channel: ".$chan_to_part." at request of ".$from_nick." with reason: ".$part_message));
		$bot->socketHandler->send($bot->ircHandler->PART($chan_to_part, $part_message));
	}
	else
	{
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "Parting channel: ".$chan_to_part." at request of ".$from_nick));
		$bot->socketHandler->send($bot->ircHandler->PART($chan_to_part, ""));
	}

}


?>


<?php

//	$channel = new Channel($channel);
//	$chan_member_me = new ChannelMember($bot->botConfig->NICKNAME);
//	$channel->AddMember($chan_member_me);
//	$bot->ChanList->AddChannel($channel);

	$channelobj = $bot->ChanList->findChannelByName($channel);
	$bot->ChanList->RemoveChannel($channelobj);

	if($channelobj == null)
	{
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "I parted $channel, but my internal register said I wasn't on it.  That's wierd!"));
	}
	else
	{
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "I parted $channel!"));
	}



?>
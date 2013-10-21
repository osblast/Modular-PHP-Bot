<?php

	$channelobj = $bot->ChanList->findChannelByName($channel);

	if($channelobj == null)
	{
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "$from_nick parted channel $channel, I'm on the channel but for some reason I'm not keeping track of it."));
	}

	$chan_member = $channelobj->MemberList->findMemberByNickname($from_nick);

	if($chan_member == null)
	{
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "$from_nick parted channel $channel, but for some reason I didn't know they were in the channel."));
	}


	$channelobj->MemberList->RemoveMember($chan_member);

	$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "Sad to see $from_nick leave $channel!  Hopefully they return soon!"));

?>
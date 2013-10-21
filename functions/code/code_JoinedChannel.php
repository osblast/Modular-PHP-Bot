<?php

	$channelobj = $bot->ChanList->findChannelByName($channel);

	if($channelobj == null)
	{
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "$from_nick joined channel $channel, I'm on the channel but for some reason I'm not keeping track of it."));
	}

	$chan_member = $channelobj->MemberList->findMemberByNickname($from_nick);

	if($chan_member != null)
	{
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "$from_nick joined channel $channel, but for some reason I already thought they were in the channel."));
	}

	$chan_member = new ChannelMember($from_nick);
	$channelobj->MemberList->AddMember($chan_member);

	$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "Welcome to $channel, $from_nick!  I hope you enjoy your stay :-)"));

?>
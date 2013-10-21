<?php

	$channelobj = new Channel($channel);
	$chan_member_me = new ChannelMember($bot->botConfig->NICKNAME);
	$channelobj->MemberList->AddMember($chan_member_me);
	$bot->ChanList->AddChannel($channelobj);

	$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "Oh boy, I joined $channel, hey all! :-)"));

?>
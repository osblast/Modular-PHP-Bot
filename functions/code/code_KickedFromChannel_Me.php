<?php

	$channelObj = $bot->ChanList->findChannelByName($channel);

	if($channelObj == null)
		return; /* nothing to do here, user was kicked from a channel that I am not keeping track of for some reason */

	$kicker_chan_member = $channelObj->MemberList->findMemberByNickname($kicker);
	$kicked_chan_member = $channelObj->MemberList->findMemberByNickname($kicked);

	if($kicked_chan_member == null)
		return; /* nothing we can do here, the user that was kicked from the channel doesn't exist in my records for some reason! */

	// REMOVE THE CHANNEL from my known repo, because I'm no longer on it.
	// simply removing it will allow for it to be recreated if/when the bot
	// rejoins that channel

	$bot->ChanList->RemoveChannel($channelObj);

	$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "User $kicker kicked ME from $channel for: $kick_reason! [bastard!]"));

?>
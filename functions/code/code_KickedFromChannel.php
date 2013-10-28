<?php

	$channelObj = $bot->ChanList->findChannelByName($channel);

	if($channelObj == null)
		return; /* nothing to do here, user was kicked from a channel that I am not keeping track of for some reason */

	$kicker_chan_member = $channelObj->MemberList->findMemberByNickname($kicker);
	$kicked_chan_member = $channelObj->MemberList->findMemberByNickname($kicked);

	if($kicked_chan_member == null)
		return; /* nothing we can do here, the user that was kicked from the channel doesn't exist in my records for some reason! */

	$channelObj->MemberList->RemoveMember($kicked_chan_member); //  remove user from channel db

	$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "User $kicker kicked $kicked from $channel for: $kick_reason!"));

?>
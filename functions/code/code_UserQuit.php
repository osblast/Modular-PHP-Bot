<?php


	$chanlist = $bot->ChanList;
	$mem_instance_arr = $chanlist->queryUserByNickname($quit_nickname);

	if($mem_instance_arr == null)
		return; /* nothing to do here, a user quit, but we aren't keeping track of that user for some reason */

	for($tzIter=0; $tzIter<count($mem_instance_arr); $tzIter++)
	{
		$chan_member = $mem_instance_arr[$tzIter];

		$channelObject = $bot->ChanList->findChannelByName($mem_instance_arr[$tzIter]->attachedChannel);

		if($channelObject != null)
		{
			// channel exists, remove user
			$channelObject->MemberList->RemoveMember($chan_member);
		}
	}

	$bot->socketHandler->send($bot->ircHandler->PRIVMSG("#phpbottest", "User $quit_nickname QUIT IRC because: $quit_reason, updated my records!"));

?>
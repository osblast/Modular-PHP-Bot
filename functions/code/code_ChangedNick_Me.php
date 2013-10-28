<?php

	$chanlist = $bot->ChanList;
	$mem_instance_arr = $chanlist->queryUserByNickname($old_nickname);

	if($mem_instance_arr == null)
		return; /* nothing to do here, a user changed nicknames, but we aren't keeping track of that user for some reason */

	for($tzIter=0; $tzIter<count($mem_instance_arr); $tzIter++)
	{
		$chan_member = $mem_instance_arr[$tzIter];

		if(strtoupper($chan_member->Nickname) == strtoupper($old_nickname))
			$chan_member->Nickname = $new_nickname;
	}

	$this->botConfig->NICKNAME = $new_nickname;

	$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "My nickname ($old_nickname) was changed to $new_nickname, updated my records!"));

?>
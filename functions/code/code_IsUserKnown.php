<?php

	$iuk_data = array_filter(explode(" ", $data));

	if(count($iuk_data) < 5)
	{
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "Please specify the channel name, Usage: ISUSERKNOWN <nickname>"));
	}

	$given_nickname = $iuk_data[4];
	$chanlist = $bot->ChanList;
	$mem_instance_arr = $chanlist->queryUserByNickname($given_nickname);

	if($mem_instance_arr == null)
	{
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "Sorry, the nickname $given_nickname is not known to me!"));
	}
	else
	{
		$onchanlist = "";
		for($tzIter=0; $tzIter<count($mem_instance_arr); $tzIter++)
		{
			$chan_member = $mem_instance_arr[$tzIter];

			$prefix_o = ""; $prefix_v = "";

			if($chan_member->IsOpped)
				$prefix_o = "(@)";

			if($chan_member->IsVoiced)
				$prefix_v = "(+)";

			if($onchanlist == "")
			{
				$onchanlist = "[$chan_member->attachedChannel/$prefix_o$prefix_v] ".$chan_member->Nickname;
			}
			else
			{
				$onchanlist = $onchanlist.", [$chan_member->attachedChannel/$prefix_o$prefix_v] ".$chan_member->Nickname;
			}
		}

		if(strtoupper($given_nickname) == strtoupper($bot->botConfig->NICKNAME))
		{
			$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "Yes of course I know $given_nickname..! Thats ME! I'm currently in ".count($mem_instance_arr)." channels: $onchanlist"));
		}
		else
		{

			$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "$given_nickname is known to me, in ".count($mem_instance_arr)." channels: $onchanlist"));
		}
	}


?>
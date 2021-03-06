<?php

$chanlist = $bot->ChanList;

$wioc_data = array_filter(explode(" ", $data));

if(count($wioc_data) < 5)
{
	$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "Please specify the channel name, Usage: WHOISONCHANNEL #Channel"));
}
else
{

	$wioc_channel = $wioc_data[4];

	if(substr($wioc_channel, 0, 1) != "#")
	{
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "Sorry, the channel $wioc_channel is not a valid channel name."));
		return;
	}

	$channelObj = $chanlist->findChannelByName($wioc_channel);

	if($channelObj == null)
	{
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "Sorry, I'm not on the channel $wioc_channel so I can't help you there!"));
		return;
	}

	$members = "";

	for($tzIter=0; $tzIter<$channelObj->MemberList->Count(); $tzIter++)
	{

		//$members = array_splice($channelObj->MemberList->mem_list->internal_list, $tzIter, 10);
		//$members = implode(", ", $members->Nickname);

		$chanmember = $channelObj->MemberList->Item($tzIter);

		if($members=="")
		{
			$members = $chanmember->Nickname;

			if($chanmember->IsOpped)
				$members = "(@)".$members;

			if($chanmember->IsVoiced)
				$members = "(+)".$members;

			if($chanmember->UnknownFlags != null)
				$members = "(".$chanmember->UnknownFlags.")".$members;

		}
		else
		{
			$prefix = "";

			if($chanmember->IsOpped)
				$prefix = "(@)";

			if($chanmember->IsVoiced)
				$prefix = "(+)";

			if($chanmember->UnknownFlags != null)
				$prefix = "(".$chanmember->UnknownFlags.")";

			$members = $members.", ".$prefix." ".$chanmember->Nickname;
		}


	}


	$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "Member List of channel $wioc_channel is: ".$members));

//	$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "The topic of channel $wtt_channel is: ".$channelObj->Topic));
//	$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "The topic of channel $wtt_channel was set by: ".$channelObj->TopicSetBy." (time=".$channelObj->TopicSetTime.")"));

}




?>
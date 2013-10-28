<?php

$chanlist = $bot->ChanList;

$wtt_data = array_filter(explode(" ", $data));

if(count($wtt_data) < 5)
{
	$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "Please specify the channel name, Usage: WHATSTHETOPIC #Channel"));
}
else
{

	$wtt_channel = $wtt_data[4];

	if(substr($wtt_channel, 0, 1) != "#")
	{
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "Sorry, the channel $wtt_channel is not a valid channel name."));
		return;
	}

	$channelObj = $chanlist->findChannelByName($wtt_channel);

	if($channelObj == null)
	{
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "Sorry, I'm not on the channel $wtt_channel so I can't help you there!"));
		return;
	}

	$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "The topic of channel $wtt_channel is: ".$channelObj->Topic));
	$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "The topic of channel $wtt_channel was set by: ".$channelObj->TopicSetBy." (time=".$channelObj->TopicSetTime.")"));

}




?>
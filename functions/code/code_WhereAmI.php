<?php

$chanlist = $bot->ChanList;

$on_channels = "";

for($tzIter=0; $tzIter<$chanlist->Count(); $tzIter++)
{
	if($on_channels == "")
	{
		$on_channels = $chanlist->Item($tzIter)->Name;
	}
	else
	{
		$on_channels = $on_channels.", ".$chanlist->Item($tzIter)->Name;
	}

}

$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "I am currently on the following channels: ".$on_channels));
?>
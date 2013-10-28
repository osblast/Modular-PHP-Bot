<?php

$nick_data = array_filter(explode(" ", $data));

if(count($nick_data) < 5)
{
	$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "You need to specify a new nickname.  usage: NICK <NewNickname>"));
}
else
{
	$nick_to_change_to = $nick_data[4];

	$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "Changing Nickname to: ".$nick_to_change_to." at request of ".$from_nick));
	$bot->socketHandler->send($bot->ircHandler->NICK($nick_to_change_to));

}


?>


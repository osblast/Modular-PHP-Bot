<?php

class Plugin_ChannelMessager extends Plugin {

	function __construct($instance_id, $channel)
	{
		$this->plugin_id = "ChannelMessager";

		$this->InstanceID = $instance_id;
		$this->plugin_config = array();
		$this->plugin_config["CHANNEL"] = $channel;
	}

	function TimerTicked($Timer, $Callback, $Environment)
	{
		$bot = $Environment["bot"];
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "Timer [ID=".$Timer->ID."] ticked and called the function "));
	}

	function MessageReceived($bot, $from_nick, $from_userid, $from_host, $recipient, $message)
	{
	}

	function NoticeReceived($bot, $from_nick, $from_userid, $from_host, $recipient, $message)
	{
	}

	function UserJoinChannel($bot, $from_nick, $from_userid, $from_host, $channel)
	{
	}

	function UserPartChannel($bot, $from_nick, $from_userid, $from_host, $channel, $reason)
	{
	}

	function BotJoinChannel($bot, $channel)
	{
	}

	function BotPartChannel($bot, $channel)
	{
	}

	function UserQuit($bot, $from_nick, $from_userid, $from_host, $reason)
	{
	}

}

?>
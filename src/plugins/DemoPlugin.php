<?php

class Plugin_DemoPlugin extends Plugin {

	function __construct($instance_id)
	{
		parent::__construct();

		$this->plugin_id = "DemoPlugin";

		$this->InstanceID = $instance_id;
	}

	function TimerTicked($Timer, $Callback, $Environment)
	{
		$bot = $Environment["bot"];
		$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "Timer [ID=".$Timer->ID."] ticked and called the function "));
	}

	function MessageReceived($bot, $from_nick, $from_userid, $from_host, $recipient, $message)
	{
		if(substr($recipient, 0, 1) != "#")
		{
			$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "DemoPlugin: User $from_nick ($from_userid@$from_host) just sent me a private message, it said: $message"));
		}
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
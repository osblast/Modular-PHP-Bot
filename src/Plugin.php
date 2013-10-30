<?php

	class Plugin {

		var $plugin_id;
		var $InstanceID;
		var $plugin_config;

		function __construct()
		{
			$this->plugin_config = array();
		}

		/*
		   NOTE: To implement Timer callback functions, you MUST follow this delegate:

		   Function($Timer, $Callback, $Environment), where :-

		   $Timer = the timer that elapsed/ticked
		   $Callback = the callback function in the timer
		   $Environment = the environment as specified to the callback

		   $Environment will contain at bare minimum the bot instance in $Environment["bot"].

		   any other environment configurations will also be stored in this array, including
		   things like $Environment["Channel"] or Nickname, for example, or whatever is required/etc.

		   --Anthonym 29/10/2013
		*/


		function MessageReceived($bot, $from_nick, $from_userid, $from_host, $recipient, $message)
		{}

		function NoticeReceived($bot, $from_nick, $from_userid, $from_host, $recipient, $message)
		{}

		function UserJoinChannel($bot, $from_nick, $from_userid, $from_host, $channel)
		{}

		function UserPartChannel($bot, $from_nick, $from_userid, $from_host, $channel, $reason)
		{}

		function BotJoinChannel($bot, $channel)
		{}

		function BotPartChannel($bot, $channel)
		{}

		function UserQuit($bot, $from_nick, $from_userid, $from_host, $reason)
		{}

	}


?>
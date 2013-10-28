<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2013
 */

	class Plugin {

		var $plugin_id;
		var $plugin_config;

		function __construct()
		{}

		function MessageReceived($bot, $from_nick, $from_userid, $from_host, $recipient, $message)
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
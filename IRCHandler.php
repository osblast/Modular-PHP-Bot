<?php


	class IRCHandler {

		function __construct()
		{}

		function NICK($nickname)
		{
			return("NICK $nickname");
		}

		function USER($nickname, $server, $username, $gecos)
		{
			return("USER $username $server $server :$gecos");
		}

		function JOIN($channel, $key=null)
		{
			if($key != null)
				return("JOIN $channel :$key");

			return("JOIN $channel");
		}

		function PART($channel, $reason=null)
		{
			if($reason != null)
				return("PART $channel :$reason");

			return("PART $channel");
		}

		function PRIVMSG($recipient, $message)
		{
			return("PRIVMSG $recipient :$message");
		}

		function NOTICE($recipient, $message)
		{
			return("NOTICE $recipient :$message");
		}

		function KICK($channel, $kicknick, $reason=null)
		{
			if($reason != null)
				return("KICK $channel $kicknick :$reason");

			return("KICK $channel $kicknick");
		}

		function PING($server)
		{
			return("PING :$server");
		}

		function PONG($server)
		{
			return("PONG :$server");
		}

	}

?>
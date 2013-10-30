<?php

include_once("src/BotFunction.php");

class Function_Config extends BotFunction {

	function __construct()
	{
		$this->FunctionIdentifier = "CONFIG";
		$this->AllowChannelMessage = true;
		$this->AllowPrivateMessage = true;
	}

	function Process($bot, $from_nick, $from_userid, $from_host, $data)
	{

		$cfg_data = explode(" ", $data);

		//:anthonym!anthonym@irc-9FA3E7D1.sbr800.nsw.optusnet.com.au PRIVMSG #rwxxr :@config list
		//include("functions/code/code_WhereAmI.php");
		$cfg_cmd = strtoupper($cfg_data[4]);
		$cfg_params = array_filter(array_splice($cfg_data, 5, count($cfg_data)-1));

		switch($cfg_cmd)
		{
			case "LIST":

				$cfg_array = $bot->botConfig->config;

				// Align:    |  6c  |      16c       |        20c          |                                            |
				// Header:   | [*]        Key                Value
				// Line:     |  1       Nickname             PHPBot
				$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, TextFormatter::Inverse().TextFormatter::Pad("[*]", 6, "c").TextFormatter::Pad("Key", 16, "c").TextFormatter::Pad("Value", 20, "c")."    ".TextFormatter::Inverse()));

				$c_config=0;

				foreach($cfg_array as $cKey => $cValue)
				{
					$c_config++;
					$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, TextFormatter::Pad($c_config, 6, "c")." ".TextFormatter::Pad($cKey, 19, "l")." ".TextFormatter::Pad($cValue, 19, "l")));
				}

				$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "*** End of /LIST ***"));

				break;

			default:

				$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "$from_nick tried to use CONFIG Command, but the function given ($cfg_cmd) is not supported by Config."));
				return;
				break;

		}

	}

}

?>
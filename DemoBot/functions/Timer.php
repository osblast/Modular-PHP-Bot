<?php

include_once("src/BotFunction.php");

class Function_Timer extends BotFunction {

	function __construct()
	{
		$this->FunctionIdentifier = "TIMER";
		$this->AllowChannelMessage = true;
		$this->AllowPrivateMessage = true;
	}

	function Process($bot, $from_nick, $from_userid, $from_host, $data)
	{

		$tmr_data = explode(" ", $data);

		//:anthonym!anthonym@irc-9FA3E7D1.sbr800.nsw.optusnet.com.au PRIVMSG #rwxxr :.timer add
		//include("functions/code/code_WhereAmI.php");
		$tmr_cmd = strtoupper($tmr_data[4]);
		$tmr_params = array_filter(array_splice($tmr_data, 5, count($tmr_data)-1));

		switch($tmr_cmd)
		{
			case "ADD":

				if(count($tmr_params) < 4)
				{
					$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "$from_nick tried to use TIMER Command, but the function given ($tmr_cmd) needs more parameters.  USAGE:  Timer ADD <TimerID> <Interval> <CallbackPluginID> <CallbackFunctionID>"));
					return;
				}

				$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "$from_nick tried to use TIMER ADD (not yet implemented, but input passes checks)"));
				break;

			case "DEL":

				if(count($tmr_params) < 1)
				{
					$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "$from_nick tried to use TIMER Command, but the function given ($tmr_cmd) needs more parameters.  USAGE:  Timer DEL <TimerID>"));
					return;
				}

				$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "$from_nick tried to use TIMER DEL (not yet implemented, but input passes checks)"));
				break;

			case "LIST":

				$timer_list = $bot->TimerList;

				//        | 5c  |     16c        |   10c    |    10c   |       20c          |        20c         |
				// Header:| [*] |    Timer ID    | Interval |  Status  | CPluginInstanceID  |      Function      |
				$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, TextFormatter::Inverse().TextFormatter::Pad("[*]", 5, "c").TextFormatter::Pad("Timer ID", 16, "c").TextFormatter::Pad("Interval", 10, "c").TextFormatter::Pad("Status", 10, "c").TextFormatter::Pad("CPluginInstanceID", 20, "c").TextFormatter::Pad("Function", 20, "c")."      ".TextFormatter::Inverse()));

				$c_timer=0;

				for($tzIter=0; $tzIter<$timer_list->Count(); $tzIter++)
				{
					$c_timer++;
					$timer = $timer_list->Item($tzIter);
					$callback_info = $timer->Callback;

					if($timer->Enable == true)
					{
						$status = "On";
					}
					else
					{
						$status = "Off";
					}

					$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, TextFormatter::Pad($c_timer, 5, "c").TextFormatter::Pad($timer->ID, 16, "l").TextFormatter::Pad($timer->Interval, 10, "c").TextFormatter::Pad($status, 10, "c")."  ".TextFormatter::Pad($callback_info->PluginInstanceID, 20, "l").TextFormatter::Pad($callback_info->callFunction, 25, "l")));
				}

				$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "*** End of /LIST ***"));

				break;

			case "START":

				if(count($tmr_params) < 1)
				{
					$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "$from_nick tried to use TIMER Command, but the function given ($tmr_cmd) needs more parameters.  USAGE:  Timer START <TimerID>"));
					return;
				}

				$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "$from_nick tried to use TIMER START (not yet implemented, but input passes checks)"));
				break;

			case "STOP":

				if(count($tmr_params) < 1)
				{
					$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "$from_nick tried to use TIMER Command, but the function given ($tmr_cmd) needs more parameters.  USAGE:  Timer STOP <TimerID>"));
					return;
				}

				$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "$from_nick tried to use TIMER STOP (not yet implemented, but input passes checks)"));
				break;

			default:

			$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "$from_nick tried to use TIMER Command, but the function given ($tmr_cmd) is not supported by timers."));
			return;
			break;

		}

	}

}

?>
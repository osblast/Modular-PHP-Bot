<?php

class Plugin_ChanUserAssist extends Plugin {

	function __construct($instance_id, $channel, $info_table, $prefix)
	{
		$this->plugin_id = "ChanUserAssist";

		$this->InstanceID = $instance_id;
		$this->plugin_config = array();

		$this->plugin_config["CHANNEL"] = $channel;
		$this->plugin_config["INFOTABLE"] = $info_table;
		$this->plugin_config["PREFIX"] = $prefix;

		$this->ReloadResponses();
	}

	function ReloadResponses()
	{
		$model_loader = new osBlast_Loader($this);
		$model = $model_loader->model("model_data", "src/plugins/ChanUserAssist/");

		$db_loader = new osBlast_Loader($model);
		$db_loader->database();

		$this->plugin_config["DataModel"] = $model;

		try
		{
			$this->plugin_config["Data"] = $model->GetPopularResponses($this->plugin_config["INFOTABLE"], 250);
		}
		catch(exception $ex)
		{
			echo("An error occured trying to load data for ChanUserAssist: ".$ex->getMessage());
			return;
		}
	}

	function GetResponse($code)
	{
		for($tzIter=0; $tzIter<count($this->plugin_config["Data"]); $tzIter++)
		{
			$dbcode = $this->plugin_config["Data"][$tzIter]["code"];

			if(strtoupper($code) == strtoupper($dbcode))
				return($this->plugin_config["Data"][$tzIter]["response"]);
		}

		return null;
	}

	function TimerTicked($Timer, $Callback, $Environment)
	{
		//$bot = $Environment["bot"];
		//$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "Timer [ID=".$Timer->ID."] ticked and called the function "));
	}

	function MessageReceived($bot, $from_nick, $from_userid, $from_host, $recipient, $message)
	{
		$prefix = $this->plugin_config["PREFIX"];

		if($prefix ==null || $prefix =="")
			$prefix = "!+";

		$message_parts = explode(" ", $message);

		if(strtoupper(substr($message, 0, strlen($prefix))) == strtoupper($prefix))
		{
			if(substr($recipient, 0, 1) == "#") //said in channel
			{
				// this means that $message =  !+ <nickname> <code>

				if(count($message_parts) < 3)
				{
					$bot->socketHandler->send($bot->ircHandler->PRIVMSG($from_nick, "ChanUserAssist: Invalid parameters, please use $prefix <sendto_nickname> <code>"));
				}
				else
				{
					$uresponse = $this->GetResponse($message_parts[2]);

					if(strtoupper($message_parts[1]) == "!SET")
					{
						//add code
						//  1     2      3
						// !ADD <code> response
						if(count($message_parts) < 4)
						{
							$bot->socketHandler->send($bot->ircHandler->PRIVMSG($from_nick, "ChanUserAssist: !ADD not enough parameters, use !ADD <code> <response>"));
							return;
						}

						$add_code = $message_parts[2];
						$add_response = implode(" ",array_splice($message_parts, 3, count($message_parts) - 3));

						//SetResponse($code, $response, $add_by)
						try
						{
							$this->plugin_config["DataModel"]->SetResponse($this->plugin_config["INFOTABLE"], $add_code, $add_response, $from_nick);
						}
						catch(exception $ex)
						{
							$bot->socketHandler->send($bot->ircHandler->PRIVMSG($from_nick, "ChanUserAssist: Failed to add/update code $add_code (".$ex->getMessage().")"));
							return;
						}

						$this->ReloadResponses();
						$bot->socketHandler->send($bot->ircHandler->PRIVMSG($from_nick, "ChanUserAssist: Successfully added/updated $add_code to response $add_response"));
						return;
					}

					if(strtoupper($message_parts[1]) == "!DEL")
					{
						//add code
						$bot->socketHandler->send($bot->ircHandler->PRIVMSG($recipient, "ChanUserAssist: Command not yet implemented, !DEL"));
						return;
					}

					if($uresponse != null)
					{
						$send_to_nick = $message_parts[1];
						$bot->socketHandler->send($bot->ircHandler->PRIVMSG($recipient, "$send_to_nick: [$message_parts[2]] $uresponse"));
						return;
					}
					else
					{
						$bot->socketHandler->send($bot->ircHandler->PRIVMSG($from_nick, "ChanUserAssist: the code you used doesn't exist yet.  You can create it using $prefix !set <code> <response>.  Other commands can also be used such as !del"));
						return;
					}

				}

			}
			else // said in private message
			{
				$bot->socketHandler->send($bot->ircHandler->PRIVMSG($from_nick, "ChanUserAssist plugin will eventually deal with this!"));
			}
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
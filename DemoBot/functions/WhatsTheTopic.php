<?php

include_once("src/BotFunction.php");

class Function_WhatsTheTopic extends BotFunction {

	function __construct()
	{
		$this->FunctionIdentifier = "WHATSTHETOPIC";
		$this->AllowChannelMessage = true;
		$this->AllowPrivateMessage = true;
	}

	function Process($bot, $from_nick, $from_userid, $from_host, $data)
	{
		include("code/code_WhatsTheTopic.php");
	}

}

?>
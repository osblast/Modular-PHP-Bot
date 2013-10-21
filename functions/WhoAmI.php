<?php

include_once("BotFunction.php");

class Function_WhoAmI extends BotFunction {

	function __construct()
	{
		$this->FunctionIdentifier = "WHOAMI";
		$this->AllowChannelMessage = true;
		$this->AllowPrivateMessage = true;
	}

	function Process($bot, $from_nick, $from_userid, $from_host, $data)
	{
		include("functions/code/code_WhoAmI.php");
	}

}

?>
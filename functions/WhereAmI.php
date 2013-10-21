<?php

include_once("BotFunction.php");

class Function_WhereAmI extends BotFunction {

	function __construct()
	{
		$this->FunctionIdentifier = "WHEREAMI";
		$this->AllowChannelMessage = true;
		$this->AllowPrivateMessage = true;
	}

	function Process($bot, $from_nick, $from_userid, $from_host, $data)
	{
		include("functions/code/code_WhereAmI.php");
	}

}

?>
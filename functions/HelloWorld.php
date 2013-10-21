<?php

	include_once("BotFunction.php");

	class Function_HelloWorld extends BotFunction {

		function __construct()
		{
			$this->FunctionIdentifier = "HELLOWORLD";
			$this->AllowChannelMessage = true;
			$this->AllowPrivateMessage = true;
		}

		function Process($bot, $from_nick, $from_userid, $from_host, $data)
		{
			include("functions/code/code_HelloWorld.php");
		}

	}

?>
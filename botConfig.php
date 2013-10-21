<?php

	class BotConfig {

		var $config;

		function __construct($nickname, $gecos, $server)
		{
			$this->config = array("NICKNAME" => $nickname, "GECOS" => $gecos, "SERVERS" => array($server), "CHANCOMMANDPREFIX" => ".");
		}

		public function __get($configKey)
		{
			return($this->config[$configKey]);
		}

	}

	class IRCServer {

		var $Hostname;
		var $Port;

		function __construct($Hostname, $Port)
		{
			$this->Hostname = $Hostname;
			$this->Port = $Port;
		}
	}


?>
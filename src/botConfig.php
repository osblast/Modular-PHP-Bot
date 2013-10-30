<?php

	class BotConfig {

		var $config;

		function __construct($nickname, $gecos, $server)
		{
			$this->config = array("NICKNAME" => $nickname, "GECOS" => $gecos, "SERVERS" => array($server));
		}

		public function __get($configKey)
		{
			return($this->config[$configKey]);
		}

		public function __set($configKey, $configValue)
		{
			$this->config[$configKey] = $configValue;
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
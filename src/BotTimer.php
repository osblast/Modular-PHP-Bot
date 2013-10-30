<?php


	class BotTimer {

		var $ID;
		var $Interval;
		var $LastTick;
		var $Callback;

		function __construct($ID, $Interval, $Enable=false, $Callback)
		{
			$this->ID = $ID;
			$this->Interval = $Interval;
			$this->Enable = $Enable;
			$this->Callback = $Callback;
		}

		function CheckTick()
		{
			if($this->Enable != true)
				return;

			$time_now = time();

			if(($time_now - $this->LastTick) >= $this->Interval)
			{
				$this->LastTick = time();
				$this->doCallback();
			}
		}

		function doCallback()
		{

			if($this->Callback != null)
				$this->Callback->Call($this);

		}

	}

	class BotTimerCallback {

		var $PluginInstanceID;
		var $Function;

		var $Environment;

		function __construct($PluginInstanceID, $callFunction, $Environment)
		{
			$this->Environment = $Environment;
			$this->PluginInstanceID = $PluginInstanceID;
			$this->callFunction = $callFunction;
		}

		function Call($Timer)
		{
			$bot = $this->Environment["bot"];

			if($bot == null)
			{
				echo("Timer Error:  Could not do callback for Timer ID ".$Timer->ID." because Environment does not provide bot.\r\n");
				return;
			}

			$plugin_instance_obj = $bot->PluginList->findPluginByInstanceId($this->PluginInstanceID);

			if($plugin_instance_obj == null)
			{
				echo("Timer Error:  Could not do callback for Timer ID ".$Timer->ID." because the plugin instance ID does not exist.\r\n");
				return;
			}

			if(!method_exists($plugin_instance_obj, $this->callFunction))
			{
				echo("Timer Error:  Could not do callback for Timer ID ".$Timer->ID." because the member function $this->callFunction does not exist in plugin instance.\r\n");
				return;
			}

			//timer callback delegate is  Function(Timer, Callback, Environment)
			$func_name = $this->callFunction;
			$plugin_instance_obj->$func_name($Timer, $this, $this->Environment);

		}
	}

?>
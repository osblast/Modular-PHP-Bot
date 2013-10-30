<?php

class PluginList {

	var $plugin_list;

	function __construct()
	{
		$this->plugin_list = new ArrayList();
	}

	function Count()
	{
		return($this->plugin_list->Length());
	}

	function Item($index)
	{
		return($this->plugin_list->Item($index));
	}

	function AddPlugin($plugin)
	{
		$this->plugin_list->Add($plugin);
	}

	function RemovePlugin($plugin)
	{
		$this->plugin_list->Remove($plugin);
	}

	function findPluginByInstanceId($plugin_instance_id)
	{
		for($tzIter=0; $tzIter<$this->plugin_list->Length(); $tzIter++)
		{

			$pluginInstance = $this->plugin_list->Item($tzIter);

			if(strtoupper($pluginInstance->InstanceID) == strtoupper($plugin_instance_id))
				return($pluginInstance);
		}

		return null;
	}

	function NotifyAllPlugins($notify_type, $data)
	{

		for($tzIter=0; $tzIter < $this->plugin_list->Length(); $tzIter++)
		{
			$plugin = $this->plugin_list->Item($tzIter);

			switch(strtolower($notify_type))
			{
				case "message":
					$plugin->MessageReceived($data["bot"], $data["from_nick"], $data["from_userid"], $data["from_host"], $data["recipient"], $data["message"]);
					break;

				case "notice":
					$plugin->NoticeReceived($data["bot"], $data["from_nick"], $data["from_userid"], $data["from_host"], $data["recipient"], $data["message"]);
					break;

				case "userjoin":
					$plugin->UserJoinChannel($data["bot"], $data["from_nick"], $data["from_userid"], $data["from_host"], $data["channel"]);
					break;

				case "userpart":
					$plugin->UserPartChannel($data["bot"], $data["from_nick"], $data["from_userid"], $data["from_host"], $data["channel"], $data["reason"]);
					break;

				case "botjoin":
					$plugin->BotJoinChannel($data["bot"], $data["channel"]);
					break;

				case "botpart":
					$plugin->BotPartChannel($data["bot"], $data["channel"]);
					break;

				case "userquit":
					$plugin->UserQuit($data["bot"], $data["from_nick"], $data["from_userid"], $data["from_host"], $data["reason"]);
					break;

				default:
					echo("Unknown Plugin Notifier Type: ".$notify_type."\r\n");
					break;
			}
		}
	}
}


?>
<?php

<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2013
 */

class Plugin_BitBucket extends Plugin {

	function __construct()
	{
		$this->plugin_id = "BitBucket";

		$this->plugin_config = array();
		$this->plugin_config["UPDATE_PORT"] = 7000;
	}

	function MessageReceived($bot, $from_nick, $from_userid, $from_host, $recipient, $message)
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

class Plugin_BitBucket_Project {

	var $ProjectID;
	var $ProjectName;

	var $UID;
	var $PWD;

	var $UpdateChannels = array();

	function __construct($project_id, $projectname, $uid, $pwd, $update_channels)
	{
		$this->ProjectID = $project_id;
		$this->ProjectName = $projectname;
		$this->UID = $uid;
		$this->PWD = $pwd;
		$this->UpdateChannels = $update_channels;
	}

}


?>


?>
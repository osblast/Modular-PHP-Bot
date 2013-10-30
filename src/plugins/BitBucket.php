<?php

class Plugin_BitBucket extends Plugin {

	var $listener_socket;

	function __construct($instanceId)
	{
		$this->plugin_id = "BitBucket";
		$this->InstanceID = $instanceId;

		$this->plugin_config = array();
		$this->plugin_config["IP"] = "106.186.121.105";
		$this->plugin_config["UPDATE_PORT"] = 7000;
		$this->plugin_config["NOTIFY_CHANNEL"] = "#osBlast";
		$this->plugin_config["SECURE_POST_PATH"] = "/push";

		$this->InitHookListener();
	}

	function InitHookListener()
	{
		$this->listener_socket = socket_create_listen();
		socket_set_nonblock($this->listener_socket);

		$this->listener_socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
		socket_bind($this->listener_socket,$this->plugin_config["IP"],$this->plugin_config["UPDATE_PORT"]);
		socket_listen($this->listener_socket);
		socket_set_nonblock($this->listener_socket);
	}

	function Timer_CheckNew_Callback($Timer, $Callback, $Environment)
	{
		$bot = $Environment["bot"];

		if(($newc = socket_accept($this->listener_socket)) !== false)
		{
			if($newc != null)
			{
				//echo "Client $newc has connected\r\n";
				$input = socket_read($newc, 2024);
				//echo("Data read from client: ".$input."\r\n");
				$this->ProcessDataIn($input, $Environment);
				//socket_close($newc);
			}
		}
	}

	function ProcessDataIn($data, $Environment)
	{
		$bot = $Environment["bot"];

		$data_lines = explode("\r\n", $data);

		$host = ""; $user_agent = ""; $accept = ""; $accept_language = ""; $accept_encoding="";
		$connection = ""; $content_type = ""; $content_length = 0; $data_received = array();
		$post_url = ""; $http_version = "";

		for($tzIter=0; $tzIter<count($data_lines); $tzIter++)
		{
			// parse each line
			$data_line= $data_lines[$tzIter];
			$line_info = explode(":", $data_lines[$tzIter]);

			if(substr($data_line, 0, 4) == "POST")
			{
				//Data read from client: POST /push HTTP/1.1
				$uri_bits = explode(" ", $data_line);
				$post_url = $uri_bits[1];
				$http_version = $uri_bits[2];

				if(strtoupper($post_url) != strtoupper($this->plugin_config["SECURE_POST_PATH"]))
				{
					// secure post path doesn't match, reject.  we can also notify of the failure reason
					$Data = array("post_url" => $post_url, "http_version" => $http_version);
					$this->NotifyChannel($Environment, $Data, true);
					return;
				}
			}

			if(count($line_info) < 2 && $data_line != "" && $data_line != null && strtoupper(substr($data_line, 0, 4) != "POST") && strtoupper(substr($data_line, 0, 4) != "GET"))
			{
				if(strtoupper(substr($data_line, 0, 8)) == "PAYLOAD=")
				{
					// got the payload.
					$payload = substr($data_line,8, strlen($data_line)-8);
					$hookData = $this->GetHookData($payload);
				}
			}
			else
			{
				switch(strtoupper($line_info[0]))
				{
					case "HOST":
						$host = $line_info[1];
						break;

					case "USER-AGENT":
						$user_agent = $line_info[1];
						break;

					case "ACCEPT":
						$accept = $line_info[1];
						break;

					case "ACCEPT-LANGUAGE":
						$accept_language = $line_info[1];
						break;

					case "ACCEPT-ENCODING":
						$accept_encoding = $line_info[1];
						break;

					case "CONNECTION":
						$connection = $line_info[1];
					break;

					case "CONTENT-TYPE":
						$content_type = $line_info[1];
					break;

					case "CONTENT-LENGTH":
						$content_length = $line_info[1];
					break;
				}
			}

		}

		if($hookData != null)
			$this->NotifyChannel($Environment, $hookData);

	}

	function NotifyChannel($Environment,$Data,$SecureCheckFail=false)
	{
		$bot = $Environment["bot"];

		$channel_to_notify = $this->plugin_config["NOTIFY_CHANNEL"];

		if($SecureCheckFail)
		{
			$fail_notify_line = "BitBucket> Received an unauthorised notification to ".$Data["post_url"]." (failed as url does not match my secure url)";
			$bot->socketHandler->send($bot->ircHandler->PRIVMSG($channel_to_notify, $fail_notify_line));
			return;
		}

		$repo_name = $Data["repository"]["name"];

		$notify_line = "BitBucket>";

		$commits = $Data["commits"];

		foreach($commits as $commit)
		{
			$notify_line = $notify_line." [commit: (".$commit["message"].") (".$commit["author"].") (".$commit["node"].") ";

			$notify_line = $notify_line."(";

			$file_line = "";

			$add_files=0;
			foreach($commit["files"] as $commit_file)
			{
				if($add_files < 5)
				{
					if($file_line == "")
					{
						$file_line = $commit_file["filename"];
					}
					else
					{
						$file_line = $file_line.",".$commit_file["filename"];
					}
				}

				$add_files++;
			}

			if($add_files > 5)
				$file_line = $file_line.",...(+".($add_files-5).")";

			if($file_line != "")
				$notify_line = $notify_line.$file_line;

			$notify_line = $notify_line.")]";
		}

		$bot->socketHandler->send($bot->ircHandler->PRIVMSG($channel_to_notify, $notify_line));
	}

	function GetHookData($payload)
	{
		$decoded_url_data = urldecode($payload);
		$json_data_decoded = json_decode($decoded_url_data, true);

		/* json data decoded now holds the data specified by the
		   bitbucket hook api.
		*/

		$hookData = array();

		/* generate variables from hook data */

		/* data that requires further processing */
		$repository = 	$json_data_decoded["repository"];
		$commits 	=	$json_data_decoded["commits"];

		/* parse repository details */
		$repository_absolute_url	=	$repository["absolute_url"];
		$repository_fork			=	$repository["fork"];
		$repository_is_private		=	$repository["is_private"];
		$repository_name			=	$repository["name"];
		$repository_owner			=	$repository["owner"];
		$repository_scm				=	$repository["scm"];
		$repository_slug			=	$repository["slug"];
		$repository_website			=	$repository["website"];


		/* parse commits */

		$commit_data = array();

		$current_commit = 0;

		foreach ($commits as $commit) {

			$commit_author			=	$commit["author"];
			$commit_branch			=	$commit["branch"];
			$commit_message			=	$commit["message"];
			$commit_node			=	$commit["node"];
			$commit_raw_author		=	$commit["raw_author"];
			$commit_raw_node		=	$commit["raw_node"];
			$commit_revision		=	$commit["revision"];
			$commit_size			=	$commit["size"];
			$commit_timestamp		=	$commit["timestamp"];
			$commit_utc_timestamp	=	$commit["utctimestamp"];

			/* remove trailing lf from message */
			$commit_message = substr($commit_message, 0, strlen($commit_message)-1);

			/* set this commit information into our returning array */
			$commit_data[$current_commit]["author"] 	= $commit_author;
			$commit_data[$current_commit]["branch"] 	= $commit_branch;
			$commit_data[$current_commit]["message"]	= $commit_message;
			$commit_data[$current_commit]["node"]		= $commit_node;
			$commit_data[$current_commit]["raw_author"]	= $commit_raw_author;
			$commit_data[$current_commit]["raw_node"]	= $commit_raw_node;
			$commit_data[$current_commit]["revision"]	= $commit_revision;
			$commit_data[$current_commit]["size"]		= $commit_size;
			$commit_data[$current_commit]["timestamp"]	= $commit_timestamp;
			$commit_data[$current_commit]["utctimestamp"]=$commit_utc_timestamp;

			$commit_files			=	$commit["files"];

				$commit_file_data = array();

				$current_file = 0;

				foreach($commit_files as $commit_file)
				{

					$commit_file_name	=	$commit_file["file"];
					$commit_file_type	=	$commit_file["type"];

					$commit_file_data[$current_file] = array("filename" => $commit_file_name, "type" => $commit_file_type);

					$current_file++;
				}

			$commit_data[$current_commit]["files"] = $commit_file_data;

			$current_commit++;
		}


		$hookData["commits"] = $commit_data;

		/* data that doesn't require further processing */
		$user		=	$json_data_decoded["user"];
		$canon_url	=	$json_data_decoded["canon_url"];

		/* populate hookData array */

		$hookData["user"]			=	$user;
		$hookData["canon_url"]		=	$canon_url;
		$hookData["commit_count"]	= 	count($commits);


		$hookData["repository"]	= array(
											"name"			=>	$repository_name,
											"fork"			=>	$repository_fork,
											"is_private"	=>	$repository_is_private,
											"owner"			=>	$repository_owner,
											"absolute_url"	=>	$repository_absolute_url,
											"scm"			=>	$repository_scm,
											"website"		=>	$repository_website,
											"slug"			=>	$repository_slug);

		return($hookData);
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

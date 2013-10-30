<?php

/* static funcs */
include_once("TextFormatter.php");
include_once("Color.php");

/* database funcs */
include_once("src/database/osBlast_DB.php");
include_once("src/database/osBlast_DB_Query.php");
include_once("src/database/osBlast_Loader.php");
include_once("src/database/osBlast_Model.php");

include_once("botConfig.php");
include_once("SockHandler.php");
include_once("IRCHandler.php");
include_once("FunctionHandler.php");
include_once("BotTimerList.php");
include_once("BotTimer.php");
include_once("ChanList.php");
include_once("Channel.php");
include_once("PluginList.php");
include_once("Plugin.php");

error_reporting(E_ALL ^ E_WARNING);

define("PHP_BOT_VERSION_MAJOR", "2");
define("PHP_BOT_VERSION_MINOR","0");
define("PHP_BOT_VERSION_REVISION","0");
define("PHP_BOT_VERSION_BUILD", "29122013");


class PHPBot {

	var $botConfig;
	var $socketHandler;
	var $ircHandler;
	var $functionHandler;

	var $TimerList;
	var $ChanList;
	var $PluginList;

	var $dataBuffer;

	var $botDirectory;

	function __construct($botConfig, $botDirectory=null)
	{
		//set_error_handler($this->Error());
		//set_exception_handler($this->Exception());
		$this->botDirectory = $botDirectory;

		$this->botConfig = $botConfig;
		$this->socketHandler = new SockHandler();
		$this->ircHandler = new IRCHandler();
		$this->dataBuffer = "";
		$this->functionHandler = new FunctionHandler();
		$this->TimerList = new BotTimerList();
		$this->PluginList = new PluginList();
		$this->ChanList = new ChanList();
	}

	function Run()
	{
		$server = $this->botConfig->SERVERS[0];

		$connected = $this->socketHandler->connect($this->botConfig->SERVERS[0]->Hostname, $this->botConfig->SERVERS[0]->Port);

		if($connected=FALSE)
			die("Error connecting to server.");

		$this->socketHandler->send($this->ircHandler->NICK($this->botConfig->NICKNAME));
		$this->socketHandler->send($this->ircHandler->USER($this->botConfig->NICKNAME, $this->botConfig->SERVERS[0]->Hostname, $this->botConfig->NICKNAME, $this->botConfig->GECOS));

		while(true)
		{
			//loop
			stream_set_timeout($this->socketHandler->socket, 1, 0);

			$data_in = $this->socketHandler->read();

			if($data_in != FALSE)
			{
				//process data
				$dataspl = explode("\r\n", $data_in);

				for($tzIter=0; $tzIter<count($dataspl); $tzIter++)
					$this->ProcessData($dataspl[$tzIter]);
			}

			$this->TimerList->CheckTickAll();
		}
	}

	function Exception($ex=null)
	{

		if($ex==null)
			return true;

		if($this->socketHandler != null)
		{
			$this->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "Error: [$ex]"));
		}
		else
		{
			echo("An error occured before the bot ran: [$ex]\r\n");
		}

		return(true);
	}

	function Error($errno=null, $errstr=null, $errfile=null, $errline=null)
	{
		if($errno == null && $errstr == null && $errfile == null && $errline==null)
			return;

		if($this->socketHandler != null)
		{
			$this->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "Error: [$errno] ($errstr) [$errfile:$errline]"));
		}
		else
		{
			echo("An error occured before the bot ran: [$errno] ($errstr) [$errfile:$errline]\r\n");
		}

		return(true);
	}

	function ProcessData($data)
	{
		if($data == "" || $data == null)
			return;

		$data_process = array_filter(explode(" ", $data));

		//PING first
		//echo("-> $data\r\n");

		switch(strtoupper($data_process[0]))
		{

			case "PING":
				$ping_from = substr($data_process[1], 1, strlen($data_process[1])-1);
				//echo("Received a PING from $ping_from\r\n");
				$this->socketHandler->send($this->ircHandler->PONG($ping_from));
				break;
		}

		/*
			:hubbard.freenode.net 376 charlietest1 :End of /MOTD command.
			:charlietest1 MODE charlietest1 :+i
			join #test12345
			:charlietest1!~charlie@d175-39-0-100.sbr800.nsw.optusnet.com.au JOIN #test12345
			:hubbard.freenode.net MODE #test12345 +ns
			:hubbard.freenode.net 353 charlietest1 @ #test12345 :@charlietest1
			:hubbard.freenode.net 366 charlietest1 #test12345 :End of /NAMES list.
			:services. MODE #test12345 -o charlietest1
			:hubbard.freenode.net NOTICE #test12345 :*** Notice -- TS for #test12345 changed
			from 1382210505 to 1264541499
			:services. MODE #test12345 +ct-s
			:ChanServ!ChanServ@services. JOIN #test12345
			:services. MODE #test12345 +o ChanServ
		*/

		/* TOPIC ON JOIN CHAN:

		   :cameron.freenode.net 332 pleb123 #defocus :Welcome to #defocus, a social channe
		   l for sensible conversations. Be nice, don't feed the trolls, and report concern
		   s, ideas, suggestions, to #defocus-ops. | Channel Guidelines: http://bit.ly/sg9S
		   nw | Thought of the Day: You can do anything, but not everything.
		   :cameron.freenode.net 333 pleb123 #defocus Adran 1382116297

	   */

		$from_p1 = array(); $from_p2 = array(); $from_nick = null; $from_userid = null; $from_host = null;

		if(isset($data_process[0]))
			$from_p1 = explode("!", $data_process[0]);

		if(isset($from_p1[1]))
			$from_p2 = explode("@", $from_p1[1]);


		if(count($from_p1) > 0 && count($from_p2) > 0)
		{
			$from_nick = substr($from_p1[0], 1, strlen($from_p1[0]));
			$from_userid = $from_p2[0];
			$from_host = $from_p2[1];
		}

		switch(strtoupper($data_process[1]))
		{

				//:anthonym2!~anthonym@d58-106-74-182.sbr800.nsw.optusnet.com.au QUIT :Client Quit
				case "QUIT":

					$quit_nickname = $from_nick;
					$quit_reason = implode(" ", array_splice($data_process, 2, (count($data_process)-2)));
					$quit_reason = substr($quit_reason, 1, (strlen($quit_reason)-1)); // remove first :

					$this->ProcessUserQuit($quit_nickname, $quit_reason);

					break;


				//:anthonym!~anthonym@pdpc/supporter/professional/anthonym KICK #plester1 plebber1
				case "KICK":

					$kicker = $from_nick;
					$channel = $data_process[2];
					$kicked = $data_process[3];
					$kick_reason = implode(" ", array_splice($data_process, 4, (count($data_process)-4)));
					$kick_reason = substr($kick_reason, 1, (strlen($kick_reason)-1)); // remove first :

					$this->ProcessUserKickedFromChannel($channel, $kicker, $kicked, $kick_reason);
					break;



				//:anthonym!~anthonym@pdpc/supporter/professional/anthonym NICK :Abacus
				case "NICK":

					$old_nickname = $from_nick;
					$new_nickname = substr($data_process[2], 1, strlen($data_process[2])-1);
					$this->ProcessNickChange($old_nickname, $new_nickname);

					break;

			//:asmarlo!~armaro@d175-39-0-100.sbr800.nsw.optusnet.com.au TOPIC #testicles0000 :
			//this
				case "TOPIC":

					$channel = $data_process[2];
					$topic = implode(" ", array_splice($data_process, 3, (count($data_process)-3)));
					$topic = substr($topic, 1, (strlen($topic)-1));
					$this->ProcessTopicChange($channel, $topic, $from_nick);

					break;


//					:cameron.freenode.net 353 pleb123 = #defocus :pleb123 crackfu blackdev1l Suterus
//					u DaemonicApathy pingfloyd AAA_awright Marverick Qcoder00 urkl Croves vitaminx J
//					guy legend2579 Nothing_Much Eluino alpharender urfriend platoscave Emi razor- fi
//					reprfHydra Shammah Calinou edgesaurus borf Dwade09-- Polarina veronica Scarberia
//					n mobileblue Kumul gzg xk_id HelenaKitty Japex agsrv_ fdd hintss deezed WormDrin
//					k janislaw s0ckpuppet BigRonnieRon Jonay a_out aspire smolten cylex zr mrtux sig
//					tau neilalexander cydd

				case "353":
					// On join channel user list (multiple per time)

					$channel = $data_process[4];

					$users = implode(" ", array_splice($data_process, 5, (count($data_process)-5)));
					$users = substr($users, 1, (strlen($users)-1));

					$a_users = explode(" ", $users);
					$this->ProcessChanUserListOnJoin($channel, $a_users);

					break;


				//:foco.lu.irc.tl 376 PHPBot :End of /MOTD command.
				case "376":

					// this signifies END OF MOTD, and Connected to IRC.
					// here we call ProcessBotConnected();
					$this->ProcessBotConnected();
					break;


				case "332":
			// 			channel topic on join
			//			:cameron.freenode.net 332 pleb123 #defocus :Welcome to #defocus, a social channe
			//			l for sensible conversations. Be nice, don't feed the trolls, and report concern
			//			s, ideas, suggestions, to #defocus-ops. | Channel Guidelines: http://bit.ly/sg9S
			//			nw | Thought of the Day: You can do anything, but not everything.

					$channel = $data_process[3];
					$topic = implode(" ", array_splice($data_process, 4, (count($data_process)-3)));
					$topic = substr($topic, 1, (strlen($topic)-1));
					$this->ProcessTopicChange($channel, $topic);

					break;

				case "333":
					//channel topic set by on join
					//:cameron.freenode.net 333 pleb123 #defocus Adran 1382116297
					$channel = $data_process[3];
					$topic_set_by = $data_process[4];
					$topic_set_time = $data_process[5];
					$this->ProcessTopicChange($channel, null, $topic_set_by, $topic_set_time);

					break;


				case "JOIN":
					$this->ProcessJoin($from_nick, $from_userid, $from_host, $data_process[2]);
					break;

				case "PART":

					if(count($data_process) > 3)
					{
						$this->ProcessPart($from_nick, $from_userid, $from_host, $data_process[2], $data_process[3]);
					}
					else
					{
						$this->ProcessPart($from_nick, $from_userid, $from_host, $data_process[2], null);
					}

					break;


				case "PRIVMSG":

					if(substr($data_process[2], 0, 1) == "#")
					{
						//channelmessage
						$this->ProcessChannelMessage($from_nick, $from_userid, $from_host, strtoupper(substr($data_process[3], 1, strlen($data_process[3]))), $data);
					}
					else
					{
						//private message
						$this->ProcessPrivateMessage($from_nick, $from_userid, $from_host, strtoupper(substr($data_process[3], 1, strlen($data_process[3]))), $data);
					}

					break;

		}
	}

	function ReloadFunctions($from_nick)
	{
		//$this->functionHandler->Reload();
	}

	function ProcessBotConnected()
	{
		//do nothing here.
	}

	function ProcessChanUserListOnJoin($channel, $users)
	{

		if(substr($channel, 0, 1) == ":")
			$channel = substr($channel, 1, strlen($channel));

		$channelObj = $this->ChanList->findChannelByName($channel);

		if($channelObj == null)
			return;

		for($tzIter=0; $tzIter<count($users); $tzIter++)
		{

			$userNick = $users[$tzIter];

			if(substr($userNick, 0, 1) == "@")
			{
				$userNick = substr($userNick, 1, (strlen($userNick)-1));
				$isOpped=true;
			}
			else
			{
				$isOpped=false;
			}

			if(substr($userNick, 0, 1) == "+")
			{
				$userNick = substr($userNick, 1, (strlen($userNick)-1));
				$isVoice=true;
			}
			else
			{
				$isVoice=false;
			}

			$rangeaz = range('a', 'z'); $rangeAZ = range('A', 'Z'); $range09 = range('0', '9');
			$all_ranges = array_merge($rangeaz, $rangeAZ, $range09);
			$UnknownFlags = null;

			$firstchar = substr($userNick, 0, 1);

			if(!in_array($firstchar, $all_ranges, true))
			{
				$UnknownFlags = substr($userNick, 0, 1);
				$userNick = substr($userNick, 1, strlen($userNick)-1);
			}

			$existing = $channelObj->MemberList->findMemberByNickname($userNick);

			if($existing == null)
			{
				$channelmember = new ChannelMember($userNick, $isOpped, $isVoice);

				if($UnknownFlags != null)
					$channelmember->UnknownFlags = $UnknownFlags;

				$channelObj->MemberList->AddMember($channelmember);
			}
		}

	}

	function ProcessUserKickedFromChannel($channel, $kicker, $kicked, $kick_reason)
	{

		if(substr($channel, 0, 1) == ":")
			$channel = substr($channel, 1, strlen($channel));

		if(strtoupper($kicked) == strtoupper($this->botConfig->NICKNAME))
		{
			$channelObj = $bot->ChanList->findChannelByName($channel);

			if($channelObj == null)
				return; /* nothing to do here, user was kicked from a channel that I am not keeping track of for some reason */

			$kicker_chan_member = $channelObj->MemberList->findMemberByNickname($kicker);
			$kicked_chan_member = $channelObj->MemberList->findMemberByNickname($kicked);

			if($kicked_chan_member == null)
				return; /* nothing we can do here, the user that was kicked from the channel doesn't exist in my records for some reason! */

			// REMOVE THE CHANNEL from my known repo, because I'm no longer on it.
			// simply removing it will allow for it to be recreated if/when the bot
			// rejoins that channel
			$bot->ChanList->RemoveChannel($channelObj);
		}
		else
		{
			$channelObj = $bot->ChanList->findChannelByName($channel);

			if($channelObj == null)
				return; /* nothing to do here, user was kicked from a channel that I am not keeping track of for some reason */

			$kicker_chan_member = $channelObj->MemberList->findMemberByNickname($kicker);
			$kicked_chan_member = $channelObj->MemberList->findMemberByNickname($kicked);

			if($kicked_chan_member == null)
				return; /* nothing we can do here, the user that was kicked from the channel doesn't exist in my records for some reason! */

			$channelObj->MemberList->RemoveMember($kicked_chan_member); //  remove user from channel db
		}
	}

	function ProcessUserQuit($quit_nickname, $quit_reason)
	{
		$bot = $this;

		if(strtoupper($quit_nickname) == strtoupper($this->botConfig->NICKNAME))
		{
			// me!
			// Nothing to do here..
		}
		else
		{
			// someone else!
			$chanlist = $bot->ChanList;
			$mem_instance_arr = $chanlist->queryUserByNickname($quit_nickname);

			if($mem_instance_arr == null)
				return; /* nothing to do here, a user quit, but we aren't keeping track of that user for some reason */

			for($tzIter=0; $tzIter<count($mem_instance_arr); $tzIter++)
			{
				$chan_member = $mem_instance_arr[$tzIter];

				$channelObject = $bot->ChanList->findChannelByName($mem_instance_arr[$tzIter]->attachedChannel);

				if($channelObject != null)
				{
					// channel exists, remove user
					$channelObject->MemberList->RemoveMember($chan_member);
				}
			}

			//Notify plugins
			//$plugin->UserQuit($data["bot"], $data["from_nick"], $data["from_userid"], $data["from_host"], $data["reason"]);
			$this->PluginList->NotifyAllPlugins("userquit", array("bot"=>$this, "from_nick" => $from_nick, "from_userid" => $from_userid, "from_host" => $from_host, "reason" => $quit_reason));
		}
	}

	function ProcessNickChange($old_nickname, $new_nickname)
	{
		$bot = $this;

		if(strtoupper($old_nickname) == strtoupper($this->botConfig->NICKNAME))
		{
			// me!
			$chanlist = $bot->ChanList;
			$mem_instance_arr = $chanlist->queryUserByNickname($old_nickname);

			if($mem_instance_arr == null)
				return; /* nothing to do here, a user changed nicknames, but we aren't keeping track of that user for some reason */

			for($tzIter=0; $tzIter<count($mem_instance_arr); $tzIter++)
			{
				$chan_member = $mem_instance_arr[$tzIter];

				if(strtoupper($chan_member->Nickname) == strtoupper($old_nickname))
					$chan_member->Nickname = $new_nickname;
			}

			$this->botConfig->NICKNAME = $new_nickname;
		}
		else
		{
			// someone else!
			$chanlist = $bot->ChanList;
			$mem_instance_arr = $chanlist->queryUserByNickname($old_nickname);

			if($mem_instance_arr == null)
				return; /* nothing to do here, a user changed nicknames, but we aren't keeping track of that user for some reason */

			for($tzIter=0; $tzIter<count($mem_instance_arr); $tzIter++)
			{
				$chan_member = $mem_instance_arr[$tzIter];

				if(strtoupper($chan_member->Nickname) == strtoupper($old_nickname))
					$chan_member->Nickname = $new_nickname;
			}
		}
	}

	function ProcessTopicChange($channel, $topic=null, $setby=null, $settime=null)
	{
		if(substr($channel, 0, 1) == ":")
			$channel = substr($channel, 1, strlen($channel));

		$channelObj = $this->ChanList->findChannelByName($channel);

		if($channelObj == null)
			return;

		if($topic != null)
			$channelObj->Topic = $topic;

		if($setby != null)
			$channelObj->TopicSetBy = $setby;

		if($settime != null)
			$channelObj->TopicSetTime = $settime;
	}

	function ProcessPart($from_nick, $from_userid, $from_host, $channel, $reason)
	{
		$bot = $this;

		if(substr($channel, 0, 1) == ":")
			$channel = substr($channel, 1, strlen($channel));

		if(strtoupper($from_nick) == strtoupper($this->botConfig->NICKNAME))
		{
			// me!
			$channelobj = $bot->ChanList->findChannelByName($channel);
			$bot->ChanList->RemoveChannel($channelobj);

			if($channelobj == null)
			{
				//$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "I parted $channel, but my internal register said I wasn't on it.  That's wierd!"));
			}
			else
			{
				//$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "I parted $channel!"));
			}

			/* notify plugins */
			$this->PluginList->NotifyAllPlugins("botpart", array("bot"=>$this, "channel" => $channel));
		}
		else
		{
			// someone else!
			$channelobj = $bot->ChanList->findChannelByName($channel);

			if($channelobj == null)
			{
				//$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "$from_nick parted channel $channel, I'm on the channel but for some reason I'm not keeping track of it."));
			}

			$chan_member = $channelobj->MemberList->findMemberByNickname($from_nick);

			if($chan_member == null)
			{
				//$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "$from_nick parted channel $channel, but for some reason I didn't know they were in the channel."));
			}


			$channelobj->MemberList->RemoveMember($chan_member);

			/* notify plugins */
			$this->PluginList->NotifyAllPlugins("userpart", array("bot"=>$this, "from_nick" => $from_nick, "from_userid" => $from_userid, "from_host" => $from_host, "channel" => $channel, "reason" => $reason));

		}

	}

	function ProcessJoin($from_nick, $from_userid, $from_host, $channel)
	{
		$bot = $this;

		if(substr($channel, 0, 1) == ":")
			$channel = substr($channel, 1, strlen($channel));

		if(strtoupper($from_nick) == strtoupper($this->botConfig->NICKNAME))
		{
			// me!
			$channelobj = new Channel($channel);
			$chan_member_me = new ChannelMember($bot->botConfig->NICKNAME);
			$channelobj->MemberList->AddMember($chan_member_me);
			$this->ChanList->AddChannel($channelobj);

			//Notify plugins
			$this->PluginList->NotifyAllPlugins("botjoin", array("bot"=>$this, "channel" => $channel));

		}
		else
		{
			// someone else!
			$channelobj = $bot->ChanList->findChannelByName($channel);

			if($channelobj == null)
			{
				//$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "$from_nick joined channel $channel, I'm on the channel but for some reason I'm not keeping track of it."));
			}

			$chan_member = $channelobj->MemberList->findMemberByNickname($from_nick);

			if($chan_member != null)
			{
				//$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel, "$from_nick joined channel $channel, but for some reason I already thought they were in the channel."));
			}

			$chan_member = new ChannelMember($from_nick);
			$channelobj->MemberList->AddMember($chan_member);

			/* notify plugins */
			$this->PluginList->NotifyAllPlugins("userjoin", array("bot"=>$this, "from_nick" => $from_nick, "from_userid" => $from_userid, "from_host" => $from_host, "channel" => $channel));
		}
	}

	function ProcessAction($from_nick, $from_userid, $from_host, $command, $data)
	{

	}

	function ProcessCTCP($from_nick, $from_userid, $from_host, $command, $data)
	{
		$ctcp_string = strtoupper(substr($command, 1, strlen($command)-1));
		$ctcp_sparts = explode(" ", $ctcp_string);

		$ctcp_function = $ctcp_sparts[0];

		if(substr($ctcp_function, strlen($ctcp_function)-1, 1) == chr(1))
			$ctcp_function = substr($ctcp_function, 0, strlen($ctcp_function)-1);

		if(file_exists('src/functions/ctcp/'.strtolower($ctcp_function).".php"))
		{
			$bot = $this;
			include('src/functions/ctcp/'.strtolower($ctcp_function).".php");
			return;
		}

		return;
	}

	function ProcessPrivateMessage($from_nick, $from_userid, $from_host, $command, $data)
	{

		//Processing Private Message: from anthonym, command=PING, data=:anthonym!anthonym@irc-9FA3E7D1.sbr800.nsw.optusnet.com.au PRIVMSG PHPBot :PING 1382979811
		//echo("Processing Private Message: from $from_nick, command=$command, data=$data\r\n");

		//notify plugins
		$dataspl = explode(" ", $data);
		$message = implode(" ",array_splice($dataspl, 3, count($dataspl)-3));
		$message = substr($message, 1, strlen($message)-1);

		$this->PluginList->NotifyAllPlugins("message", array("bot"=>$this, "from_nick" => $from_nick, "from_userid" => $from_userid, "from_host" => $from_host, "recipient" => $dataspl[2], "message" => $message));

		if(strtoupper($command) == "RELOAD")
		{
			$this->ReloadFunctions($from_nick);
		}

		if(substr(strtoupper($command), 0, 1) == chr(1) && (substr(strtoupper($command), 1, 6) != "ACTION"))
		{
			/* CTCP Command */
			$this->ProcessCTCP($from_nick, $from_userid, $from_host, $command, $data);
			return;
		}
		elseif(substr(strtoupper($command), 1, 6) != "ACTION")
		{
			/* ACTION */
			$this->ProcessAction($from_nick, $from_userid, $from_host, $command, $data);
			return;
		}

		if($this->functionHandler != null)
		{
			$aFunction = $this->functionHandler->findByCommand($command);

			if($aFunction == null)
			{
				$this->socketHandler->send($this->ircHandler->PRIVMSG($this->botConfig->DebugChannel, "$from_nick tried to use command $command, however that command is unknown to me."));
				return;
			}

			//		function Process($bot, $from_nick, $from_userid, $from_host, $data)

			if($aFunction->AllowPrivateMessage)
				$aFunction->Process($this, $from_nick, $from_userid, $from_host, $data);
		}

	}

	function ProcessChannelMessage($from_nick, $from_userid, $from_host, $command, $data)
	{

		$dataspl = explode(" ", $data);
		$message = implode(" ",array_splice($dataspl, 3, count($dataspl)-3));
		$message = substr($message, 1, strlen($message)-1);

		//notify plugins
		$this->PluginList->NotifyAllPlugins("message", array("bot"=>$this, "from_nick" => $from_nick, "from_userid" => $from_userid, "from_host" => $from_host, "recipient" => $dataspl[2], "message" => $message));

		$this->botConfig->ChanFunctionPrefix == null ? $cmd_prefix = "." : $cmd_prefix = $this->botConfig->ChanFunctionPrefix;

		if(strtoupper($command) == $cmd_prefix."RELOAD")
		{
			$this->ReloadFunctions($from_nick);
			return;
		}

		if(substr(strtoupper($command), 0, 1) == chr(1))
		{
			$this->ProcessCTCP($from_nick, $from_userid, $from_host, $command, $data);
			return;
		}

		if(substr(strtoupper($command), 0, 1) != strtoupper($cmd_prefix))
			return;  // not a channel command, its not prefixed.

		$command = substr($command, 1, strlen($command)); // now we can remove the prefix.

		if($this->functionHandler != null)
		{

			$aFunction = $this->functionHandler->findByCommand($command);

			if($aFunction == null)
			{
				$this->socketHandler->send($this->ircHandler->PRIVMSG($this->botConfig->DebugChannel, "$from_nick tried to use command $command, however that command is unknown to me."));
				return;
			}

			//		function Process($bot, $from_nick, $from_userid, $from_host, $data)
			if($aFunction->AllowChannelMessage)
				$aFunction->Process($this, $from_nick, $from_userid, $from_host, $data);

			return;
		}
	}
}


?>
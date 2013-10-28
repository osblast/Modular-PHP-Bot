<?php

include_once("botConfig.php");
include_once("SockHandler.php");
include_once("IRCHandler.php");
include_once("FunctionHandler.php");
include_once("ChanList.php");
include_once("Channel.php");

//set_error_handler("BotError");

define("PHP_BOT_VERSION_MAJOR", "2");
define("PHP_BOT_VERSION_MINOR","0");
define("PHP_BOT_VERSION_REVISION","0");
define("PHP_BOT_VERSION_BUILD", "29122013");

function BotError()
{
	return(true);
}

class PHPBot {

	var $botConfig;
	var $socketHandler;
	var $ircHandler;
	var $functionHandler;

	var $ChanList;

	var $dataBuffer;

	function __construct($botConfig)
	{
		$this->botConfig = $botConfig;
		$this->socketHandler = new SockHandler();
		$this->ircHandler = new IRCHandler();
		$this->dataBuffer = "";
		$this->functionHandler = new FunctionHandler();
		$this->ChanList = new ChanList();
	}

	function Run()
	{

		$this->ReloadFunctions();

		$server = $this->botConfig->SERVERS[0];

		$connected = $this->socketHandler->connect($this->botConfig->SERVERS[0]->Hostname, $this->botConfig->SERVERS[0]->Port);

		if($connected=FALSE)
			die("Error connecting to server.");

		$this->socketHandler->send($this->ircHandler->NICK($this->botConfig->NICKNAME));
		$this->socketHandler->send($this->ircHandler->USER($this->botConfig->NICKNAME, $this->botConfig->SERVERS[0]->Hostname, $this->botConfig->NICKNAME, $this->botConfig->GECOS));

		while(true)
		{
			//loop
			$data_in = $this->socketHandler->read();

			if($data_in != FALSE)
			{
				//process data
				$dataspl = explode("\r\n", $data_in);

				for($tzIter=0; $tzIter<count($dataspl); $tzIter++)
					$this->ProcessData($dataspl[$tzIter]);
			}

		}
	}

	function ProcessData($data)
	{
		if($data == "" || $data == null)
			return;

		$data_process = array_filter(explode(" ", $data));

		//PING first
		echo("-> $data\r\n");

		switch(strtoupper($data_process[0]))
		{

			case "PING":
				$ping_from = substr($data_process[1], 1, strlen($data_process[1])-1);
				echo("Received a PING from $ping_from\r\n");
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

	function ReloadFunctions()
	{
		$this->functionHandler->Reload();
	}

	function DoReload($from_nick)
	{
		if($this->functionHandler != null)
		{
			$this->socketHandler->send($this->ircHandler->PRIVMSG($this->botConfig->DebugChannel, "$from_nick called RELOAD .., Reloading functions .."));
			$arrFunctionList = $this->functionHandler->Reload();

			$functionsLoaded = implode(", ", $arrFunctionList);

			$this->socketHandler->send($this->ircHandler->PRIVMSG($this->botConfig->DebugChannel, "successfully reloaded ".count($arrFunctionList)." functions: [$functionsLoaded]"));

		}
		else
		{
			$this->socketHandler->send($this->ircHandler->PRIVMSG($this->botConfig->DebugChannel, "$from_nick tried to RELOAD, however the functionhandler doesn't exist."));
			return;
		}
	}

	function ProcessBotConnected()
	{
		$bot = $this;
		include("functions/code/code_BotConnected.php");
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


			$existing = $channelObj->MemberList->findMemberByNickname($userNick);

			if($existing == null)
			{
				$channelmember = new ChannelMember($userNick, $isOpped, $isVoice);
				$channelObj->MemberList->AddMember($channelmember);
			}
		}

	}

	function ProcessUserKickedFromChannel($channel, $kicker, $kicked, $kick_reason)
	{
		$bot = $this;

		if(substr($channel, 0, 1) == ":")
			$channel = substr($channel, 1, strlen($channel));

		if(strtoupper($kicked) == strtoupper($this->botConfig->NICKNAME))
		{
			// me!
			include("functions/code/code_KickedFromChannel_Me.php");
		}
		else
		{
			// someone else!
			include("functions/code/code_KickedFromChannel.php");
		}
	}

	function ProcessUserQuit($quit_nickname, $quit_reason)
	{
		$bot = $this;

		if(strtoupper($quit_nickname) == strtoupper($this->botConfig->NICKNAME))
		{
			// me!
			include("functions/code/code_UserQuit_Me.php");
		}
		else
		{
			// someone else!
			include("functions/code/code_UserQuit.php");
		}
	}

	function ProcessNickChange($old_nickname, $new_nickname)
	{
		$bot = $this;

		if(strtoupper($old_nickname) == strtoupper($this->botConfig->NICKNAME))
		{
			// me!
			include("functions/code/code_ChangedNick_Me.php");
		}
		else
		{
			// someone else!
			include("functions/code/code_ChangedNick.php");
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
			include("functions/code/code_PartedChannel_Me.php");
		}
		else
		{
			// someone else!
			include("functions/code/code_PartedChannel.php");
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
			include("functions/code/code_JoinedChannel_Me.php");
		}
		else
		{
			// someone else!
			include("functions/code/code_JoinedChannel.php");
		}
	}

	function ProcessPrivateMessage($from_nick, $from_userid, $from_host, $command, $data)
	{

		//Processing Private Message: from anthonym, command=PING, data=:anthonym!anthonym@irc-9FA3E7D1.sbr800.nsw.optusnet.com.au PRIVMSG PHPBot :PING 1382979811
		//echo("Processing Private Message: from $from_nick, command=$command, data=$data\r\n");

		if(strtoupper($command) == "RELOAD")
		{
			$this->DoReload($from_nick);
		}

		if(substr(strtoupper($command), 0, 1) == chr(1))
		{
			$ctcp_string = strtoupper(substr($command, 1, strlen($command)-1));
			$ctcp_sparts = explode(" ", $ctcp_string);

			$ctcp_function = $ctcp_sparts[0];

			if(substr($ctcp_function, strlen($ctcp_function)-1, 1) == chr(1))
				$ctcp_function = substr($ctcp_function, 0, strlen($ctcp_function)-1);

			if(file_exists('functions/ctcp/'.strtolower($ctcp_function).".php"))
			{
				$bot = $this;
				include('functions/ctcp/'.strtolower($ctcp_function).".php");
				return;
			}

			$this->socketHandler->send($this->ircHandler->PRIVMSG($this->botConfig->DebugChannel, "$from_nick tried to use CTCP command |$ctcp_function|, however that CTCP command is unknown to me."));
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
		//$this->socketHandler->send($this->ircHandler->PRIVMSG($this->botConfig->DebugChannel, "$from_nick ($from_userid@$from_host) tried to use command: $command, and said $data"));

		if(strtoupper($command) == ".RELOAD")
		{
			$this->DoReload($from_nick);
			return;
		}

		if(substr(strtoupper($command), 0, 1) == chr(1))
		{
			$ctcp_string = strtoupper(substr($command, 1, strlen($command)-1));
			$ctcp_sparts = explode(" ", $ctcp_string);

			$ctcp_function = $ctcp_sparts[0];

			if(substr($ctcp_function, strlen($ctcp_function)-1, 1) == chr(1))
				$ctcp_function = substr($ctcp_function, 0, strlen($ctcp_function)-1);

			if(file_exists('functions/ctcp/'.strtolower($ctcp_function).".php"))
			{
				$bot = $this;
				include('functions/ctcp/'.strtolower($ctcp_function).".php");
				return;
			}

			$this->socketHandler->send($this->ircHandler->PRIVMSG($this->botConfig->DebugChannel, "$from_nick tried to use CTCP command $command, however that CTCP command is unknown to me."));
			return;

		}

		if(substr(strtoupper($command), 0, 1) != strtoupper($this->botConfig->CHANCOMMANDPREFIX))
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
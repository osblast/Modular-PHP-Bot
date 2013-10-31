<?php

	/* include bot core */
	include_once("src/bot.php");

	/* include any plugin file(s) that we need */
	//include_once('src/plugins/BitBucket.php');
	include_once('src/plugins/DemoPlugin.php');
	include_once('src/plugins/ChanUserAssist.php');

	class DemoBot extends PHPBot {

		function __construct($nickname, $gecos, $ircserver, $port, $debugchan)
		{
			parent::__construct(new BotConfig($nickname, $gecos, new IRCServer($ircserver, $port)));

			$this->botConfig->DebugChannel = $debugchan;
			$this->botConfig->ChanFunctionPrefix = "@";

			//$this->PluginList->AddPlugin(new Plugin_BitBucket("BitBucketDemo"));
			//$this->TimerList->AddTimer(new BotTimer("tmrBitBucketDemo", 1, true, new BotTimerCallback("BitBucketDemo", "Timer_CheckNew_Callback", array("bot" => $this))));
			$this->PluginList->AddPlugin(new Plugin_DemoPlugin("DemoPlugin"));
			$this->PluginList->AddPlugin(new Plugin_ChanUserAssist("ChanUserAssist", "#osBlast", "osblastcua", "!+"));

			$this->functionHandler->Reload("DemoBot/functions/");
		}

		function ProcessPrivateMessage($from_nick, $from_userid, $from_host, $command, $data)
		{
			/* always run parent code before processing your own - this will handle default ctcp/etc */
			parent::ProcessPrivateMessage($from_nick, $from_userid, $from_host, $command, $data);
		}

		function ProcessChannelMessage($from_nick, $from_userid, $from_host, $command, $data)
		{
			/* always run parent code before processing your own - this will handle default ctcp/etc */
			parent::ProcessChannelMessage($from_nick, $from_userid, $from_host, $command, $data);
		}

		function ProcessAction($from_nick, $from_userid, $from_host, $command, $data)
		{
			/* always run parent code before processing your own */
			parent::ProcessAction($from_nick, $from_userid, $from_host, $command, $data);
		}

		function ProcessCTCP($from_nick, $from_userid, $from_host, $command, $data)
		{
			/* always run parent code before processing your own */
			parent::ProcessCTCP($from_nick, $from_userid, $from_host, $command, $data);
		}

		function ProcessJoin($from_nick, $from_userid, $from_host, $channel)
		{
			/* always run parent code before processing your own */
			parent::ProcessJoin($from_nick, $from_userid, $from_host, $channel);
		}

		function ProcessPart($from_nick, $from_userid, $from_host, $channel, $reason)
		{
			/* always run parent code before processing your own */
			parent::ProcessPart($from_nick, $from_userid, $from_host, $channel, $reason);
		}

		function ProcessTopicChange($channel, $topic=null, $setby=null, $settime=null)
		{
			/* always run parent code before processing your own */
			parent::ProcessTopicChange($channel, $topic, $setby, $settime);
		}

		function ProcessNickChange($old_nickname, $new_nickname)
		{
			/* always run parent code before processing your own */
			parent::ProcessNickChange($old_nickname, $new_nickname);
		}

		function ProcessUserQuit($quit_nickname, $quit_reason)
		{
			/* always run parent code before processing your own */
			parent::ProcessUserQuit($quit_nickname, $quit_reason);
		}

		function ProcessUserKickedFromChannel($channel, $kicker, $kicked, $kick_reason)
		{
			/* always run parent code before processing your own */
			parent::ProcessUserKickedFromChannel($channel,$kicker,$kicked,$kick_reason);
		}

		function ProcessBotConnected()
		{
			$this->socketHandler->send($this->ircHandler->JOIN($this->botConfig->DebugChannel));
		}

		function ReloadFunctions($from_nick)
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

	}

?>
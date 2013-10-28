<?php

	include_once("bot.php");
	include_once("botConfig.php");

	$pid = pcntl_fork();
	if ($pid == -1) {

		echo('Could not fork PHPBot into the background, running in the foreground.  When you CLOSE this process, the bot will EXIT..\r\n');

		run_bot();

	} else if ($pid) {
		// we are the parent
		pcntl_wait($status); //Protect against Zombie children
	} else {
		// we are the child
		run_bot($pid);
	}

	function run_bot($process_id=null)
	{

		if($process_id != null)
		{
			echo('Forking PHPBot into the background, Process ID: '.$process_id.'..');
		}
		else
		{
			echo('Running PHPBot in the foreground of this session only..');
		}

		$ircServer = new IRCServer("irc.tl", 6667);
		$ircBotConfig = new botConfig("PHPBot", "Modular PHP Bot", $ircServer);
		$ircBotConfig->DebugChannel = "#rwxxr";
		$ircBot = new PHPBot($ircBotConfig);
		$ircBot->Run();
	}

?>
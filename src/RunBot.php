<?php

class RunBot {

	var $ProcessID;
	var $Bot;

	function __construct($Bot)
	{
		$this->Bot = $Bot;
	}

	function Go()
	{
		$this->ProcessID = pcntl_fork();

		if ($this->ProcessID == -1)
		{
			$this->Execute();

		} else if ($this->ProcessID)
		{	// we are the parent
			pcntl_wait($status); //Protect against Zombie children
		} else
		{	// we are the child
			$this->Execute();
		}
	}

	function Execute()
	{
		if($this->ProcessID != null)
		{
			echo("Forking PHPBot into the background, Process ID: ".$this->ProcessID."..\r\n");
		}
		else
		{
			echo("Could not fork PHPBot into the background, running in the foreground.  When you CLOSE this process, the bot will EXIT..\r\n");
		}

		$this->Bot->Run();
	}
}

?>
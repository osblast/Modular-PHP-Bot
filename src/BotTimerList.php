<?php

class BotTimerList {

	var $timer_list;

	function __construct()
	{
		$this->timer_list = new ArrayList();
	}

	function Count()
	{
		return($this->timer_list->Length());
	}

	function Item($index)
	{
		return($this->timer_list->Item($index));
	}

	function AddTimer($timer)
	{
		$this->timer_list->Add($timer);
	}

	function RemoveTimer($timer)
	{
		$this->timer_list->Remove($timer);
	}

	function CheckTickAll()
	{
		for($tzIter=0; $tzIter<$this->timer_list->Length(); $tzIter++)
		{
			$timer = $this->timer_list->Item($tzIter);
			$timer->CheckTick();
		}
	}

	function findTimerById($timer_id)
	{
		for($tzIter=0; $tzIter<$this->timer_list->Length(); $tzIter++)
		{

			$timer = $this->timer_list->Item($tzIter);

			if(strtoupper($timer->ID) == strtoupper($timer_id))
				return($timer);
		}

		return null;
	}
}


?>
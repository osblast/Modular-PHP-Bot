<?php

class ArrayList {

	var $internal_list = array();

	function ArrayList()
	{
		$this->internal_list = array();
	}

	function Add($object)
	{
		array_push($this->internal_list, $object);
	}

	function Remove($object)
	{
		for ($tzSearch=0; $tzSearch<count($this->internal_list); $tzSearch++)
		{
			if ($this->internal_list[$tzSearch] === $object)
			{
				array_splice($this->internal_list, $tzSearch, 1);
				return true;
			}
		}

		return false;
	}

	function Length()
	{
		return count($this->internal_list);
	}

	function Item($index)
	{
		return $this->internal_list[$index];
	}

	function SetItem($index, $item)
	{
		$this->internal_list[$index] = $item;
	}

}

?>
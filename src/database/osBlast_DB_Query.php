<?php

class osBlast_DB_Query {

	var $handle;

	function __construct()
	{
	}

	function Prepare($link, $statement)
	{
		$this->handle = $link->prepare($statement);
	}

	function Bind($index, $value, $type)
	{
		//$handle->bindValue(1, 100, PDO::PARAM_INT);
		$this->handle->bindValue($index, $value, $type);
	}

	function Execute()
	{
		$this->handle->execute();
	}

	private function Fetch($type, $all=FALSE)
	{
		if($all)
		{
			//PDO::FETCH_OBJ
			return($this->handle->fetchAll($type));
		}
		else
		{
			//PDO::FETCH_OBJ
			return($this->handle->fetch($type));
		}
	}

	function FetchArray($returnBoth=FALSE)
	{
		$arr_rtn = array();

		if($returnBoth)
		{
			$arr_rtn = $this->Fetch(PDO::FETCH_BOTH, false);
		}

		$arr_rtn = $this->Fetch(PDO::FETCH_ASSOC, false);

		if(is_array($arr_rtn))
			return($arr_rtn);

		return(array());
	}

	function FetchAllArray($returnBoth=FALSE)
	{
		$arr_rtn = array();

		if($returnBoth)
		{
			$arr_rtn = $this->Fetch(PDO::FETCH_BOTH, true);
		}

		$arr_rtn = $this->Fetch(PDO::FETCH_ASSOC, true);

		if(is_array($arr_rtn))
			return($arr_rtn);

		return(array());
	}

	function FetchAllObject()
	{
		return($this->Fetch(PDO::FETCH_OBJ, true));
	}

}

?>
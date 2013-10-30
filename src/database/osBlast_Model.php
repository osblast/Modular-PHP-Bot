<?php

class osBlast_Model {

	var $db;
	var $load;
	var $dbloaded=FALSE;

	var $path;

	public function __construct()
	{
		$this->path = "data/";
		$this->load = new osBlast_Loader($this);
	}

	function __sleep()
	{
		return(array('load', 'dbloaded'));
	}

	function __wakeup()
	{
		if($this->dbloaded)
		{
			$this->load->parent = $this;
			$this->load->database();
		}
	}

}

?>
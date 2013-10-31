<?php

class osBlast_Loader {

	var $parent;

	function __construct($parent=null)
	{
		if($parent != null)
			$this->parent = $parent;
	}

	function model($model_name, $model_path)
	{

		if(!file_exists($model_path.$model_name.".php"))
		{
			throw new Exception("No model found for ".$model_name." in data path.");
			return null;
		}

		include_once($model_path.$model_name.".php");

		$_mdl_name = ucfirst($model_name);

		if(get_parent_class($_mdl_name) !== "osBlast_Model")
			throw new Exception("Model prototype not found for the model you are attempting to use (".$model_name.").  Please ensure that you have created it, and it is placed in your site model directory.");

		$mdl_name_loaded = strtolower($model_name);

		return(new $_mdl_name());
	}

	function database()
	{
		$db = new osBlast_DB();

		if($this->parent !== null)
		{
			$this->parent->db = $db;
			$this->parent->dbloaded = TRUE;
		}

		return($db);
	}

}

?>
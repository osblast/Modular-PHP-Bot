<?php

class Model_data extends osBlast_Model {

	var $path;

	public function __construct()
	{
		$this->path = "data/";
		$this->load = new osBlast_Loader($this);
	}

	public function GetPopularResponses($table, $limit=50)
	{
		try
		{
			//	function get_where($tblName = '', $dMatch = array(), $conditions=null)

			$query = $this->db->get($table, "LIMIT ".$limit);
		}
		catch (exception $ex)
		{
			throw new Exception("An error occured trying to access the $table table, please create this table.  You can find the default schema in the CHanUserAssist plugin directory in src/plugins/.  Simply import this structure into a table. (error=$ex)");
			return;
			//throw new Exception("Error retrieving data from database.");
		}

		return($query->FetchAllArray());
	}

	public function SetResponse($table, $code, $response, $add_by)
	{
		try
		{
			$query = $this->db->insert($table, array("code"=>$code, "response"=>$response, "add_by" => $add_by), " ON DUPLICATE KEY UPDATE response='".$response."', modify_by='".$add_by."'");
		}
		catch (exception $ex)
		{
			throw new Exception("An error occured trying to access the $table table, please create this table.  You can find the default schema in the CHanUserAssist plugin directory in src/plugins/.  Simply import this structure into a table. (error=$ex)");
			return;
		}

		return true;
	}

}

?>
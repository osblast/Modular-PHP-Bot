<?php

class osBlast_DB {

	var $link;

	function osBlast_DB()
	{
		//$dbHost = osBlast::getSiteInfo()->getConfig()->Database["Hostname"];
		//$dbUser = osBlast::getSiteInfo()->getConfig()->Database["Username"];
		//$dbPass = osBlast::getSiteInfo()->getConfig()->Database["Password"];
		//$dbName = osBlast::getSiteInfo()->getConfig()->Database["Dbname"];

		if($dbHost == "" || $dbUser == "" || $dbPass == "" || $dbName == "")
			throw new Exception("Configuration incomplete, no database configuration exists.  Please consult osBlast Configuration Guide");

		try
		{
			//$this->link = new PDO('mysql:host='.$dbHost.';dbname='.$dbName, $dbUser, $dbPass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_PERSISTENT => false, PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8mb4'));
			$this->link = new PDO('mysql:host='.$dbHost.';dbname='.$dbName, $dbUser, $dbPass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_PERSISTENT => false, PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8'));
		}
		catch(Exception $ex)
		{
			throw new Exception("Could not create PDO object");
		}

		return($this);
	}


	function get($tblName, $conditions=null)  /* get all records in assoc array for table */
	{
		$query = new osBlast_DB_Query();
		$query->Prepare($this->link, "SELECT * From ".$tblName." $conditions;");
		$query->Execute();

		return($query);
	}

	function get_where($tblName = '', $dMatch = array(), $conditions=null)
	{
		// $dMatch is $dMatch['field'] = 'value';

		if($tblName === '')
			throw new Exception("No table name was given to get_where");


		$tbl_fields = array_keys($dMatch);

		if(count($tbl_fields) < 1)
			throw new Exception("No parameters were specified to get_where");

		//string implode ( string $glue , array $pieces )

		$sql_query = "SELECT * FROM ".$tblName." WHERE ";

		for($tzIter=0; $tzIter < count($tbl_fields); $tzIter++)
		{
			if($tzIter > 0)
			{
				$sql_query .= "AND ".$tbl_fields[$tzIter]."='".$dMatch[$tbl_fields[$tzIter]]."'";
			}
			else
			{
				$sql_query .= $tbl_fields[$tzIter]."='".$dMatch[$tbl_fields[$tzIter]]."'";
			}
		}

		$sql_query .= " $conditions;";

		$query = new osBlast_DB_Query();
		$query->Prepare($this->link, $sql_query);
		$query->Execute();

		return($query);
	}

	function insert($tblName = '', $values = array(), $condition=null)
	{

		$query = new osBlast_DB_Query();
		// $values[column]=value
		$cols = array_keys($values);

		$sql_query = "INSERT into $tblName (".implode(",", $cols).") VALUES ('".implode("','", $values)."') $condition;";

		$query->Prepare($this->link, $sql_query);
		$query->Execute();


		return(TRUE);
		//die("Need to perform sql str: ".$sql_str);
	}

	//UPDATE Users SET firstname='Bob', lastname='Harris' WHERE facebook_user_id='606488861';

	function update($tblName = '', $values = array())
	{
		$query = new osBlast_DB_Query();

		$cols = array_keys($values);

		$sql_query = "UPDATE $tblName SET ";
		$sql_query_adj = "";

		for($tzIter=0; $tzIter<count($cols); $tzIter++)
		{
			if($sql_query_adj == "")
			{
				$sql_query_adj .= $cols[$tzIter]."='".$values[$cols[$tzIter]]."'";
			}
			else
			{
				$sql_query_adj .= ", ".$cols[$tzIter]."='".$values[$cols[$tzIter]]."'";
			}
		}

		$sql_query = $sql_query .= $sql_query_adj . ";";

		$query->Prepare($this->link, $sql_query);
		$query->Execute();

		return(TRUE);
	}

	function update_where($tblName = '', $where_values, $values = array())
	{
		$query = new osBlast_DB_Query();

		$wvals = array_keys($where_values);
		$cols = array_keys($values);

		$sql_query = "UPDATE $tblName SET ";
		$sql_query_adj = "";

		for($tzIter=0; $tzIter<count($cols); $tzIter++)
		{
			if($sql_query_adj == "")
			{
				$sql_query_adj .= $cols[$tzIter]."='".$values[$cols[$tzIter]]."'";
			}
			else
			{
				$sql_query_adj .= ", ".$cols[$tzIter]."='".$values[$cols[$tzIter]]."'";
			}
		}

		$sql_query_wheres = "";

		for($tzIter=0; $tzIter<count($wvals); $tzIter++)
		{
			if($sql_query_wheres == "")
			{
				$sql_query_wheres .= $wvals[$tzIter]."='".$values[$wvals[$tzIter]]."'";
			}
			else
			{
				$sql_query_wheres .= " AND ".$wvals[$tzIter]."='".$values[$wvals[$tzIter]]."'";
			}
		}


		$sql_query = $sql_query .= $sql_query_adj . " WHERE ".$sql_query_wheres.";";

		$query->Prepare($this->link, $sql_query);
		$query->Execute();

		return(TRUE);
	}


}


?>
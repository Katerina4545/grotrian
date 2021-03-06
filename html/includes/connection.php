<?php

require_once("statement.php");

Class Connection
{
	var $_dbLink;

	function Connection($server = '', $databaseName = '', $userName = '', $password = '')
	{
		$this->_dbLink = mssql_connect($server, $userName, $password);
		if ($this->_dbLink === false)
		{
			trigger_error('Can\'t connect to database server.', E_USER_ERROR);
		}
		else
		{
			if (mssql_select_db($databaseName, $this->_dbLink) === false)
				trigger_error('Can\'t select database.', E_USER_ERROR);
		}
//		mssql_query("SET NAMES cp1251;", $this->_dbLink);
	}

	function &CreateStatement($resultType = MSSQL_ASSOC)
	{
		$stmt = new Statement($this->_dbLink, $resultType);
		return $stmt;
	}


	static function GetSQLString($str)
	{
		return "'".mssql_escape_string($str)."'";
	}
	
	function GetSQLLike($str)
	{
		return addcslashes(mssql_escape_string($str), "\\_%'");
	}

	function GetSQLArray($arr)
	{
		if (is_array($arr))
		{
			foreach ($arr as $key => $value)
				$arr[$key] = Connection::GetSQLString($value);
		}
		return $arr;
	}

}

?>
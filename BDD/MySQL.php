<?php
/*
 *    fichier  :  MySQL.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  13 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require_once 'IBDD.php';

class MySQL extends SGBD
{
	private $cnn;
	
	function __destruct()
	{
		if ($this->cnn != null)
			$this->disconnect();
	}

	function connect()
	{
		$this->cnn = mysql_connect($this->_Server,$this->_User,$this->_Password, true, 131074);
		mysql_select_db($this->_Base, $this->cnn);
	}
	
	function disconnect()
	{
		mysql_close($this->cnn);
		$this->cnn = null;
	}
	
	function num_rows($result)
	{
		return mysql_num_rows($result);
	}
	
	function query($sql)
	{
		//echo $sql."<hr>";
		return mysql_query($sql, $this->cnn);
	}
	
	function fetch_array($resultat)
	{
		return mysql_fetch_array($resultat);
	}
	
	public function Execute(StoredProcedure $sp)
	{
		$aQuery = explode(";\\", $this->BuildQuery($sp));
		foreach ($aQuery as $query)
		{
			$result = $this->query($query);
			if (!$result)
			{
				echo mysql_error($this->cnn);
			}
		}
		 
		return $result;
	}
	
	/*private function BuildQuery(StoredProcedure $sp)
	{
		$sCommand = "CALL ".$sp->ProcedureName." (";
		
		$dom = new DomDocument();
		$dom->load("Config/Descripteur/".$sp->ProcedureName.".xml");
		$xpath = new DOMXPath($dom);
		$root = $dom->documentElement;
		$nodeInputParameters = $root->getElementsByTagName("Input")->item(0);
		if ($nodeInputParameters->hasChildNodes())
		{
			$nodesParameters = $root->getElementsByTagName("Input")->item(0)->childNodes;
			
			foreach ($nodesParameters as $node)
			{
				if ($node->nodeType == XML_ELEMENT_NODE)
				{
					$sParameterName = $node->getAttribute("Name");
					$sParameterType = $node->getAttribute("Type");
					
					if ($sParameterType == "VA")
						$sCommand .= "'".$sp->GetParameterValue($sParameterName)."', ";
					else
						$sCommand .= $sp->GetParameterValue($sParameterName).", ";
				}
			}
			
			$sCommand = substr($sCommand, 0, -2);
		}
		$sCommand .= ")";
		echo $sCommand."<br>";
		return $sCommand;
	}*/
	
	private function BuildQuery(StoredProcedure $sp)
	{
		$dom = new DomDocument();
		$dom->load("Config/Descripteur/".$sp->ProcedureName.".xml");
		$xpath = new DOMXPath($dom);
		$root = $dom->documentElement;
		$sCommand = $dom->getElementsByTagName("Instruction")->item(0)->nodeValue;
		
		$nodeInputParameters = $root->getElementsByTagName("Input")->item(0);
		if ($nodeInputParameters->hasChildNodes())
		{
			$nodesParameters = $root->getElementsByTagName("Input")->item(0)->childNodes;
			
			foreach ($nodesParameters as $node)
			{
				if ($node->nodeType == XML_ELEMENT_NODE)
				{
					$sParameterName = $node->getAttribute("Name");
					$sParameterType = $node->getAttribute("Type");
					$sValue = $sp->GetParameterValue($sParameterName);
					switch ($sParameterType)
					{
						case "I":
							if ($sValue == "" || $sValue == null)
								$sValue = "NULL";
							break;
						case "VA":
							$sValue = "'$sValue'";
							break;
						case "B":
							$sValue = ($sValue) ? 1 : 0;
							break;
						case "DT":
							$sValue = "'".date("Y-m-d H:i", $sValue)."'";
							break;
					}
					
					$sCommand = str_replace("@$sParameterName", $sValue, $sCommand);
				}
			}
		}

		//echo $sCommand."<br>";

		return $sCommand;
	}
}

?>
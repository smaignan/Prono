<?php
/*
 *    fichier  :  Request.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  17 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

class Request
{
	var $aParam = array();

	public function __construct()
	{
		$this->Load();
	}
	
	private function Load()
	{
		$this->aParam = $_GET;
		
		if ($_SERVER["REQUEST_METHOD"] == "POST")
		{
			$this->aParam = array_merge($this->aParam, $_POST);
			
			$sParam = (isset($this->aParam["Param"])) ? $this->aParam["Param"] : "";
			
			unset($this->aParam["Param"]);
			
			if ($sParam != "")
			{
				$aParam = explode("&",$sParam);
				foreach($aParam as $sValue)
				{
					list($key, $value) = explode("=", $sValue);
					if (isset($this->aParam[$key]))
					{
						if ($this->aParam["$key"] != $value)
						{
							echo "Request : Cette clé existe déjà.";
							exit;
						}
					}
					$this->aParam[$key] = $value;
				}
			}
		}
	}
	
	public function GetParam($key)
	{
		if (array_key_exists($key, $this->aParam))
			return $this->aParam[$key];
		else
			return "";
	}
}
?>

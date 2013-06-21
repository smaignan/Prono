<?php
/*
 *    fichier  :  StoredProcedure.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  18 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

class StoredProcedure
{
	public $Connection = null;
	public $ProcedureName = "";
	private $_parameters = array();
	
	public function __construct($ProcedureName)
	{
		$this->ProcedureName = $ProcedureName;
		
		//On recherche la connexion liée à cette procédure
		/*$dom = new DomDocument();
		$dom->load("Config/sp.xml");
		$xpath = new DOMXPath($dom);
		$root = $dom->documentElement;
		$procedures = $xpath->evaluate('Procedure[@Id="'.$ProcedureName.'"]', $root);

		if ($procedures->length == 0)
		{
			echo "La procédure stockée demandée n'existe pas : $ProcedureName<br>";
		}
		else if ($procedures->length > 1)
		{
			echo "Procédure stockée en double";
		}
		else
		{
			$procedure = $procedures->item(0);
			$iModeBouchonne = $procedure->getElementsByTagName("Bouchon")->item(0)->nodeValue;
		}*/
	}
	
	public function AddParameters($key, $value)
	{
		$this->_parameters[$key] = $value;
	}
	
	public function GetParameterValue($key)
	{
		if (array_key_exists($key, $this->_parameters))
		{
			return $this->_parameters[$key];
		}
		else
		{
			echo "Le paramètre '$key' n'existe pas dans le descripteur.";
			return false;
		}
	}
}
?>
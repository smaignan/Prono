<?php
/*
 *    fichier  :  Connection.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  18 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

class Connection
{
	private static $_cnn = null;
	private $_aParam = array(); 
	
	public function __construct($Code)
	{
		//On recherche la connexion li�e � cette proc�dure
		$dom = new DomDocument();
		$dom->load("Config/BDD.xml");
		$xpath = new DOMXPath($dom);
		$root = $dom->documentElement;
		
		if ($Code == "DEFAULT")
		{
			//On choisit la connexion par d�faut
			$nodeDefault = $xpath->evaluate('Default', $root)->item(0);
			$this->LoadConnectionByNode($nodeDefault);
		}
		else
		{
			$nodesConnection = $xpath->evaluate('Connection[@Id="'.$Code.'"]', $root);
			if ($nodesConnection->length == 0)
			{
				echo "Aucune connexion ne correspond � ce code.";
			}
			else if ($nodesConnection->length > 1)
			{
				echo "Plusieurs connexions correspondent � ce code.";
			}
			else
			{
				$this->LoadConnectionByNode($nodesConnection->item(0));
			}
		}
	}
	
	private function LoadConnectionByNode($node)
	{
		$this->_aParam["Driver"] = $node->getElementsByTagName("Driver")->item(0)->nodeValue;
		$this->_aParam["Server"] = $node->getElementsByTagName("Server")->item(0)->nodeValue;
		$this->_aParam["Base"] = $node->getElementsByTagName("Base")->item(0)->nodeValue;
		$this->_aParam["User"] = $node->getElementsByTagName("User")->item(0)->nodeValue;
		$this->_aParam["Password"] = $node->getElementsByTagName("Password")->item(0)->nodeValue;
	}
	
	public function GetParam($key)
	{
		return $this->_aParam[$key];
	}
}
?>

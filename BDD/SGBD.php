<?php
/*
 *    fichier  :  SGBD.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  13 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

class SGBD {
	public $StoredProcedure = "";
	protected $_Server = "";
	protected $_Base = "";
	protected $_User = "";
	protected $_Password = "";
	
	public static function GetInstance()
	{
		$dom = new DomDocument();
		$dom->load("Config/BDD.xml");
		$xpath = new DOMXPath($dom);
		$root = $dom->documentElement;
		
		$sDriver = $root->getElementsByTagName("Driver")->item(0)->nodeValue;
		
		$sClassName = $sDriver;
		require_once $sClassName.'.php';
		$bdd = new $sClassName;
		
		$bdd->_Server = $root->getElementsByTagName("Server")->item(0)->nodeValue;
		$bdd->_Base = $root->getElementsByTagName("Base")->item(0)->nodeValue;
		$bdd->_User = $root->getElementsByTagName("User")->item(0)->nodeValue;
		$bdd->_Password = $root->getElementsByTagName("Password")->item(0)->nodeValue;
		
		return $bdd;
	}
	
	public static function GetTimeStampFromBDD($Date)
	{
		return strtotime($Date);
		/*echo $Date."<br>";
		list($Annee, $Mois, $Jour) = explode("-", $Date);
		return mktime(0, 0, 0, $Mois, $Jour, $Annee);*/
	}
	
	public static function GetTimeStampFromView($Date)
	{
		if (strpos($Date, " ") === false)
		{
			$sDate = $Date;
			$Heure = 0;
			$Minute = 0;
		}
		else
		{
			list($sDate, $sTime) = explode(" ", $Date);
			list($Heure, $Minute) = explode(":", $sTime);
		}
		
		list($Jour, $Mois, $Annee) = explode("/", $sDate);
		
		return mktime($Heure, $Minute, 0, $Mois, $Jour, $Annee);
	}
	
	public static function GetBooleanFromBDD($Value)
	{
		return $Value;
	}
	
	public static function GetBooleanFromView($Value)
	{
		return $Value;
	}
}
?>
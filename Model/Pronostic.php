<?php
/*
 *    fichier  :  Pronostic.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  31 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/BDD/SGBD.php";

class model_Pronostic
{
	public $PronoEquipe = null;
	public $PronoVisiteur = null;
	public $Risque = 0;
	public $Aveugle = false;
	
	/*
	 * Description : Charge un objet pronostic
	 * Paramètres  : Ligne de résultat 
	 */
	function LoadPronosticByRecord($row)
	{
		$this->PronoEquipe = $row["pro_prono"];
		$this->PronoVisiteur = $row["pro_prono_v"];
		$this->Risque = $row["pro_risques"];
		$this->Aveugle = ($row["juti_aveugle"] == "t");
	}
	
	/*
	 * Description : Retourne un objet pronostic
	 * Paramètres  : Ligne de résultat
	 */
	public static function GetPronosticByRecord($row)
	{
		$pronostic = new model_Pronostic();
		$pronostic->LoadPronosticByRecord($row);
		return $pronostic;
	}
	
	public static function ExplodeScore($Score)
	{
		$iScoreEquipe = substr($Score, 0, 1);
		$iScoreVisiteur = substr($Score, -1);
		
		return array($iScoreEquipe, $iScoreVisiteur);
	}
}
?>

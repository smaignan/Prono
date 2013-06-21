<?php
/*
 *    fichier  :  JourneeUtilisateur.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  4 févr. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/BDD/SGBD.php";

class model_JourneeUtilisateur
{
	public $FilRouge = false;
	public $GrilleAveugle = false;
	public $Points = null;
	public $Bonus = 0;
	public $Malus = 0;
	
	/*
	 * Description : Charge un objet journée utilisateur
	 * Paramètres  : Ligne de résultat 
	 */
	function LoadJourneeUtilisateurByRecord($row)
	{
		$this->FilRouge = $row["juti_fil_rouge"];
		$this->GrilleAveugle = $row["juti_aveugle"];
		$this->Points = $row["juti_points"];
		$this->Bonus = $row["juti_bonus"];
		$this->Malus = $row["juti_malus"];
	}
	
	/*
	 * Description : Retourne un objet journée utilisateur
	 * Paramètres  : Ligne de résultat
	 */
	public static function GetJourneeUtilisateurByRecord($row)
	{
		$jouUti = new model_JourneeUtilisateur();
		$jouUti->LoadJourneeUtilisateurByRecord($row);
		return $jouUti;
	}
}
?>

<?php
/*
 *    fichier  :  Equipe.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  9 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/BDD/SGBD.php";

class model_Equipe
{
	var $Id;
	var $Nom;
	
	public static function GetEquipe($id, $nom)
	{
		$equipe = new model_Equipe();
		$equipe->Id = $id;
		$equipe->Nom = $nom;
		return $equipe;
	}
	
	/* Description : Charge un objet equipe
	 * Param�tres  : Ligne de r�sultat 
	 */
	function LoadEquipeByRecord($row)
	{
		$this->Id = $row["equ_id"];
		$this->Nom = $row["equ_nom"];
	}
	
	/*
	 * Description : Retourne un objet equipe
	 * Param�tres  : Ligne de r�sultat
	 */
	public static function GetEquipeByRecord($row)
	{
		$equipe = new model_Equipe();
		$equipe->LoadEquipeByRecord($row);
		return $equipe;
	}
	
	/*
	 * Description : Retourne une �quipe par rapport � un identifiant
	 * Param�tres  : Identifiant de l'�quipe
	 */
	public static function GetEquipeById($Id)
	{
		$sp = new StoredProcedure("sp_GetEquipeById");
		$sp->AddParameters("Id", $Id);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
				
		if (!$result)
		{
			echo "Erreur lors de la r�cup�ration d'une �quipe.";
			exit;
		}

		$equipe = self::GetEquipeByRecord($bdd->fetch_array($result));
		
		unset($bdd);
		
		return $equipe;
	}
	
	/*
	 * Description : Retourne la liste des �quipes
	 */
	function Liste()
	{
		$sp = new StoredProcedure("sp_ListeEquipe");
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		$bdd->disconnect();
		
		if (!$result)
		{
			echo "Erreur lors de la r�cup�ration de la liste des �quipes.";
			exit;
		}
		
		$aEquipe = array();
		while ($row = $bdd->fetch_array($result))
			$aEquipe[] = self::GetEquipeByRecord($row);
		
		unset($bdd);
		
		return $aEquipe;
	}
		
	/*
	 * Description : Ajout d'une �quipe
	 */
	function Add()
	{
		$sp = new StoredProcedure("sp_AddEquipe");
		$sp->AddParameters("Nom", $this->Nom);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors de l'ajout d'une �quipe.";
			exit;
		}
	}

	/*
	 * Description : Mise � jour d'une �quipe
	 */	
	function Update()
	{
		$sp = new StoredProcedure("sp_UpdateEquipe");
		$sp->AddParameters("Id", $this->Id);
		$sp->AddParameters("Nom", $this->Nom);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		
		$result = $bdd->Execute($sp);
		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors de la mise � jour d'une �quipe.";
			exit;
		}
	}
	
	/*
	 * Description : Suppression d'un utilisateur
	 */
	function Delete()
	{
		$sp = new StoredProcedure("sp_DeleteEquipe");
		$sp->AddParameters("Id", $this->Id);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors de la suppression d'une �quipe.";
			exit;
		}
	}
}
?>

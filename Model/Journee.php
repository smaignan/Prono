<?php
/*
 *    fichier  :  Journee.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  23 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/BDD/SGBD.php";
require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Equipe.php";
require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Match.php";

class model_Journee
{
	public $Id = 0;
	public $Numero = 0;
	public $MatchRetour = false;
	public $DateFin;
	public $DiffuserPronostics = false;
	public $DiffuserResultats = false;
	public $DateJournee;
	
	/*
	 * Description : Charge un objet journee
	 * Param�tres  : Ligne de r�sultat 
	 */
	function LoadJourneeByRecord($row)
	{
		$this->Id = $row["jou_id"];
		$this->Numero = $row["jou_numero"];
		$this->DateFin = SGBD::GetTimeStampFromBDD($row["jou_date"]);
		$this->MatchRetour = SGBD::GetBooleanFromBDD($row["jou_retour"]);
		$this->DiffuserPronostics = SGBD::GetBooleanFromBDD($row["jou_diffuser_pronostics"]);
		$this->DiffuserResultats = SGBD::GetBooleanFromBDD($row["jou_diffuser_resultats"]);
		$this->DateJournee = SGBD::GetTimeStampFromBDD($row["jou_date_journee"]);
	}
	
	/*
	 * Description : Retourne un objet journee
	 * Param�tres  : Ligne de r�sultat
	 */
	public static function GetJourneeByRecord($row)
	{
		if ($row == null)
		{
			$journee = null;
		}
		else
		{
			$journee = new model_Journee();
			$journee->LoadJourneeByRecord($row);
		}
		
		return $journee;
	}
	
	/*
	 * Description : Retourne une journ�e par rapport � un identifiant
	 * Param�tres  : Identifiant de la journ�e
	 */
	public static function GetJourneeById($Id)
	{
		$sp = new StoredProcedure("sp_GetJourneeById");
		$sp->AddParameters("Id", $Id);

		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		if (!$result)
		{
			echo "Erreur lors de la r�cup�ration d'une journ�e.";
			exit;
		}

		$journee = self::GetJourneeByRecord($bdd->fetch_array($result));
		
		unset($bdd);
		
		return $journee;
	}
	
	/*
	 * Description : Retourne la derni�re journ�e
	 * Param�tres  : Num�ro de la journ�e
	 */
	public static function GetLastJourneePronostics()
	{
		$sp = new StoredProcedure("sp_GetLastJourneePronostics");

		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		if (!$result)
		{
			echo "Erreur lors de la r�cup�ration d'une journ�e.";
			exit;
		}

		$journee = null;
		if ($bdd->num_rows($result) != 0)
			$journee = self::GetJourneeByRecord($bdd->fetch_array($result));
		
		unset($bdd);
		
		return $journee;
	}
	
	/*
	 * Description : Retourne la derni�re journ�e
	 * Param�tres  : Num�ro de la journ�e
	 */
	public static function GetLastJourneePronosticsAdmin()
	{
		$sp = new StoredProcedure("sp_GetLastJourneePronosticsAdmin");

		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		if (!$result)
		{
			echo "Erreur lors de la r�cup�ration d'une journ�e.";
			exit;
		}

		$journee = null;
		if ($bdd->num_rows($result) != 0)
			$journee = self::GetJourneeByRecord($bdd->fetch_array($result));
		
		unset($bdd);
		
		return $journee;
	}
	
	/*
	 * Description : Retourne la derni�re journ�e
	 * Param�tres  : Num�ro de la journ�e
	 */
	public static function GetLastJourneeResultats()
	{
		$sp = new StoredProcedure("sp_GetLastJourneeResultats");

		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		if (!$result)
		{
			echo "Erreur lors de la r�cup�ration d'une journ�e.";
			exit;
		}

		$journee = null;
		if ($bdd->num_rows($result) != 0)
			$journee = self::GetJourneeByRecord($bdd->fetch_array($result));
		
		unset($bdd);
		
		return $journee;
	}
	
	/*
	 * Description : Retourne la prochaine journ�e
	 * Param�tres  : Num�ro de la journ�e
	 */
	public static function GetNextJournee()
	{
		$sp = new StoredProcedure("sp_GetNextJournee");

		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		if (!$result)
		{
			echo "Erreur lors de la r�cup�ration d'une journ�e.";
			exit;
		}

		$journee = self::GetJourneeByRecord($bdd->fetch_array($result));
		
		unset($bdd);
		
		return $journee;
	}
	
	/*
	 * Description : Retourne la derni�re journ�e
	 */
	public static function GetDerniereJournee()
	{
		$sp = new StoredProcedure("sp_GetDerniereJournee");

		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		if (!$result)
		{
			echo "Erreur lors de la r�cup�ration de la derni�re journ�e.";
			exit;
		}

		$journee = self::GetJourneeByRecord($bdd->fetch_array($result));
		
		unset($bdd);
		
		return $journee;
	}
		
	/*
	 * Retourne la liste des journ�es
	 */
	public static function Liste()
	{
		$sp = new StoredProcedure("sp_ListeJournee");
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		$bdd->disconnect();
		
		if (!$result)
		{
			echo "Erreur lors de la r�cup�ration de la liste des journ�es.";
			exit;
		}
		
		$aJournee = array();
		while ($row = $bdd->fetch_array($result))
			$aJournee[] = self::GetJourneeByRecord($row);
		
		unset($bdd);
		
		return $aJournee;
	}
	
	/*
	 * Retourne la liste des journ�es en rappel
	 */
	public static function Rappel()
	{
		$sp = new StoredProcedure("sp_Rappel");
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		$bdd->disconnect();
		
		if (!$result)
		{
			echo "Erreur lors de la r�cup�ration des journ�es de rappel.";
			exit;
		}
		
		$aJournee = array();
		while ($row = $bdd->fetch_array($result))
			$aJournee[] = self::GetJourneeByRecord($row);
		
		unset($bdd);
		
		return $aJournee;
	}
	
	/*
	 * Retourne la liste des journ�es
	 */
	public static function ListeJourneePronostics()
	{
		$sp = new StoredProcedure("sp_ListeJourneePronostics");
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		$bdd->disconnect();
		
		if (!$result)
		{
			echo "Erreur lors de la r�cup�ration de la liste des journ�es.";
			exit;
		}
		
		$aJournee = array();
		while ($row = $bdd->fetch_array($result))
			$aJournee[] = self::GetJourneeByRecord($row);
		
		unset($bdd);
		
		return $aJournee;
	}
	
	/*
	 * Retourne la liste des journ�es
	 */
	public static function ListeJourneeResultats()
	{
		$sp = new StoredProcedure("sp_ListeJourneeResultats");
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		$bdd->disconnect();
		
		if (!$result)
		{
			echo "Erreur lors de la r�cup�ration de la liste des journ�es.";
			exit;
		}
		
		$aJournee = array();
		while ($row = $bdd->fetch_array($result))
			$aJournee[] = self::GetJourneeByRecord($row);
		
		unset($bdd);
		
		return $aJournee;
	}
	
	public function ListeMatch()
	{
		return model_Match::Liste($this->Id);
	}
		
	/*
	 * Description : Retourne la liste des �quipes disponibles lors de la journ�e
	 */
	function EquipeDisponible()
	{
		$sp = new StoredProcedure("sp_ListeEquipeDisponible");
		$sp->AddParameters("IdJournee", $this->Id);
		
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
			$aEquipe[] = model_Equipe::GetEquipeByRecord($row);
		unset($bdd);
		
		return $aEquipe;
	}
	
	/*
	 * Description : Ajout d'une journ�e
	 */
	function Add()
	{
		$sp = new StoredProcedure("sp_AddJournee");
		$sp->AddParameters("Numero", $this->Numero);
		$sp->AddParameters("MatchRetour", SGBD::GetBooleanFromView($this->MatchRetour));
		$sp->AddParameters("DateFin", $this->DateFin);
		$sp->AddParameters("DiffuserPronostics", SGBD::GetBooleanFromView($this->DiffuserPronostics));
		$sp->AddParameters("DiffuserResultats", SGBD::GetBooleanFromView($this->DiffuserResultats));
		$sp->AddParameters("DateJournee", $this->DateJournee);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		
		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors de l'ajout d'une journ�e.";
			exit;
		}
	}

	/*
	 * Description : Mise � jour d'une journ�e
	 */	
	function Update()
	{
		$sp = new StoredProcedure("sp_UpdateJournee");
		$sp->AddParameters("Id", $this->Id);
		$sp->AddParameters("Numero", $this->Numero);
		$sp->AddParameters("MatchRetour", $this->MatchRetour);
		$sp->AddParameters("DateFin", $this->DateFin);
		$sp->AddParameters("DiffuserPronostics", $this->DiffuserPronostics);
		$sp->AddParameters("DiffuserResultats", $this->DiffuserResultats);
		$sp->AddParameters("DateJournee", $this->DateJournee);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		
		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors de la mise � jour d'une journ�e.";
			exit;
		}
	}
	
	/*
	 * Description : Suppression d'une journ�e
	 */
	function Delete()
	{
		$sp = new StoredProcedure("sp_DeleteJournee");
		$sp->AddParameters("Id", $this->Id);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);

		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors de la suppression d'une journ�e.";
			exit;
		}
	}
		
	function Calculer()
	{
		$sp = new StoredProcedure("sp_CalculerJournee");
		$sp->AddParameters("Id", $this->Id);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);

		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors du calcul d'une journ�e.";
			exit;
		}
	}
	
	public static function CalculRisque($IdJournee)
	{
		$sp = new StoredProcedure("sp_CalculRisque");
		$sp->AddParameters("IdJournee", $IdJournee);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);

		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors du calcul des risques de la journ�e.";
			exit;
		}
	}
}
?>

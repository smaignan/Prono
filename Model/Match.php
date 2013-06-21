<?php
/*
 *    fichier  :  Match.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  23 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/BDD/SGBD.php";
require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Equipe.php";

class model_Match
{
	public $Id = 0;
	public $IdEquipe = 0;
	public $IdVisiteur = 0;
	public $IdJournee = 0;
	public $Equipe = null;
	public $Visiteur = null;
	public $ScoreEquipe = "";
	public $ScoreVisiteur = "";
	public $Statistiques = array();
	
	/*
	 * Description : Charge un objet match
	 * Paramètres  : Ligne de résultat 
	 */
	function LoadMatchByRecord($row)
	{
		$this->Id = $row["ren_id"];
		$this->IdEquipe = $row["ren_equ_id"];
		$this->IdVisiteur = $row["ren_equ_id_v"];
		$this->IdJournee = $row["ren_jou_id"];
		$this->ScoreEquipe = $row["ren_result"];
		$this->ScoreVisiteur = $row["ren_result_v"];
		$this->Equipe = model_Equipe::GetEquipe($row["equ_id"], $row["equ_nom"]);
		$this->Visiteur = model_Equipe::GetEquipe($row["equ_id_v"], $row["equ_nom_v"]);
		//$this->Equipe = model_Equipe::GetEquipeById($this->IdEquipe);
		//$this->Visiteur = model_Equipe::GetEquipeById($this->IdVisiteur);
	}
	
	/*
	 * Description : Retourne un objet match
	 * Paramètres  : Ligne de résultat
	 */
	public static function GetMatchByRecord($row)
	{
		$match = new model_Match();
		$match->LoadMatchByRecord($row);
		return $match;
	}
	
	/*
	 * Description : Retourne un objet match
	 * Paramètres  : Identifiant du match
	 */
	public static function GetMatchById($Id)
	{
		$sp = new StoredProcedure("sp_GetMatchById");
		$sp->AddParameters("Id", $Id);
		echo "Id = $Id<hr>";
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		
		if (!$result)
		{
			echo "Erreur lors de la récupération du match.";
			exit;
		}

		$match = self::GetMatchByRecord($bdd->fetch_array($result));
		
		unset($bdd);
		
		return $match;
	}
	
	/*
	 * Description : Retourne la liste des matchs pour une journée
	 * Paramètres  : Numéro de la journée
	 */
	public static function Liste($IdJournee)
	{
		$sp = new StoredProcedure("sp_ListeMatch");
		$sp->AddParameters("IdJournee", $IdJournee);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		$bdd->disconnect();
		
		if (!$result)
		{
			echo "Erreur lors de la récupération de la liste des matchs.";
			exit;
		}
		
		$aMatch = array();
		while ($row = $bdd->fetch_array($result))
			$aMatch[] = self::GetMatchByRecord($row);
		
		unset($bdd);
		
		return $aMatch;
	}
	
	/*
	 * Description : Ajout d'un match
	 */
	function Add()
	{
		$sp = new StoredProcedure("sp_AddMatch");
		$sp->AddParameters("IdEquipe", $this->IdEquipe);
		$sp->AddParameters("IdVisiteur", $this->IdVisiteur);
		$sp->AddParameters("IdJournee", $this->IdJournee);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors de l'ajout d'un match.";
			exit;
		}
	}
	
	/*
	 * Description : Suppression d'un match
	 */
	function Delete()
	{
		$sp = new StoredProcedure("sp_DeleteMatch");
		$sp->AddParameters("Id", $this->Id);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors de la suppression du match.";
			exit;
		}
	}
	
	public function UpdateScore()
	{
		$sp = new StoredProcedure("sp_UpdateScore");
		$sp->AddParameters("Id", $this->Id);
		$sp->AddParameters("ScoreEquipe", $this->ScoreEquipe);
		$sp->AddParameters("ScoreVisiteur", $this->ScoreVisiteur);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors de la mise à jour des scores.";
			exit;
		}
	}
	
	public function CalculRisque($IdJournee)
	{
		$sp = new StoredProcedure("sp_UpdateRisqueAveugle");
		$sp->AddParameters("IdMatch", $this->Id);
		$sp->AddParameters("IdJournee", $IdJournee);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		
		if (!$result)
		{
			echo "Erreur lors de la mise à jour des risques aveugle";
			exit;
		}
		
		unset($bdd);
		
		$aProc = array("Victoire", "Nul", "Defaite");
		foreach ($aProc as $proc)
			$this->CalculRisqueProc($IdJournee, $proc);
	}
	
	public function CalculRisqueProc($IdJournee, $Proc)
	{
		$aParam = array(
			"2" => "4",
			"4" => "3",
			"6" => "2",
			"8" => "1"
		);
		
		$sp = new StoredProcedure("sp_Compte".$Proc);
		$sp->AddParameters("IdMatch", $this->Id);
		$sp->AddParameters("IdJournee", $IdJournee);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		
		if (!$result)
		{
			echo "Erreur lors de la récupération du nombre de ".$Proc;
			exit;
		}
		
		$row = $bdd->fetch_array($result);
		$nb_victoire = $row["nb"];
		$iRisque = 0;
		
		while (list($nb, $risque) = each($aParam))
		{
			if ($nb_victoire < $nb)
			{
				$iRisque = $risque;
				break;
			}
		}
		
		$sp = new StoredProcedure("sp_UpdateRisque".$Proc);
		$sp->AddParameters("Risque", $iRisque);
		$sp->AddParameters("IdMatch", $this->Id);
		$sp->AddParameters("IdJournee", $IdJournee);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		
		if (!$result)
		{
			echo "Erreur lors de la mise à jour des risques des ".$Proc;
			exit;
		}

		unset($bdd);
	}

	private function getRisque($point)
	{
		$aParam = array(
			"2" => "4",
			"4" => "3",
			"6" => "2",
			"8" => "1"
		);

		while (list($key, $value) = each($aParam))
		{
			if ($point < $key) return $value;
		}
	}

	public function LoadStatistiques()
	{
		$aStatistiques = array();

		$sp = new StoredProcedure("sp_GetVictoire");
		$sp->AddParameters("Id", $this->Id);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		$row = $bdd->fetch_array($result);
		$this->Statistiques['victoire'] = array('nombre' => $row['nb'], 'risque' => $this->getRisque($row['nb']));
		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors de la récupération du nombre de victoire.";
			exit;
		}

		$sp = new StoredProcedure("sp_GetNul");
		$sp->AddParameters("Id", $this->Id);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		$row = $bdd->fetch_array($result);
		$this->Statistiques['nul'] = array('nombre' => $row['nb'], 'risque' => $this->getRisque($row['nb']));
		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors de la récupération du nombre de nul.";
			exit;
		}

		$sp = new StoredProcedure("sp_GetDefaite");
		$sp->AddParameters("Id", $this->Id);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		$row = $bdd->fetch_array($result);
		$this->Statistiques['defaite'] = array('nombre' => $row['nb'], 'risque' => $this->getRisque($row['nb']));
		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors de la récupération du nombre de défaite.";
			exit;
		}
	}
}

?>

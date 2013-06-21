<?php
/*
 *    fichier  :  Resultat.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  30 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Journee.php";
require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Utilisateur.php";

class ctrl_Resultat extends Controller
{
	var $_Mode = "";
	
	public function Load()
	{
		if ($this->GetParam("lbJournee") == "")
		{
			if (UserInfo::get('Administrateur'))
				$journee = model_Journee::GetLastJourneePronostics();
			else
				$journee = model_Journee::GetLastJourneeResultats();
			
			if ($journee == "")
			{
				echo "Début de saison, aucun résultat disponible.";
				UserInfo::un_set('Module');
				exit;
			}
		}
		else
		{
			$journee = model_Journee::GetJourneeById($this->GetParam("lbJournee"));
		}
		
		if ($journee->DiffuserResultats && $this->_Mode == "")
		{
			$aUtilisateur = array();
			$aResultat = array();
			$aMatch = array();
		}
		else
		{
			$aUtilisateur = model_Utilisateur::Liste();
			$aResultat = array();
			foreach ($aUtilisateur as $utilisateur)
			{
				$aResultat[] = array(
					"Utilisateur" => $utilisateur,
					"Prono" => $utilisateur->ListePronostic($journee->Id),
					"JourneeUtilisateur" => $utilisateur->GetJourneeUtilisateur($journee->Id),
					"EtatFilRouge" => $utilisateur->EtatFilRouge($journee)
				);
			}
			
			$aMatch = $journee->ListeMatch();
		}
		
		if (UserInfo::get('Administrateur'))
			$aJournee = model_Journee::Liste();
		else
			$aJournee = $journee->ListeJourneeResultats();
		
		$sMode = (UserInfo::get('Administrateur')) ? "ADM" : "";
		
		$this->AddData("Mode", $sMode);
		$this->AddData("Resultat", $aResultat);
		$this->AddData("Journee", $journee);
		$this->AddData("ListeMatch", $aMatch);
		$this->AddData("ListeJournee", $aJournee);
	}
	
	public function lbDiffuser_Click()
	{
		Controller::Redirect("diffuser&IdDiffuser=".$this->GetParam("IdDiffuser")."&Type=".$this->GetParam("Type"));
	}
	
	public function lbCalculer_Click()
	{
		$journee = model_Journee::GetJourneeById($this->GetParam("IdJournee"));
		$aMatch = $journee->ListeMatch();
		foreach ($aMatch as $match)
		{
			$sScore = $this->GetParam("tbResultat_".$match->Id);
			if ($sScore != "")
			{
				$aScore = explode(" - ", $sScore);
				$match->ScoreEquipe = $aScore[0];
				$match->ScoreVisiteur = $aScore[1];
				$match->UpdateScore();
			}
		}
		
		$journee->Calculer();
	}
	
	public function lbJournee_Change()
	{
		
	}
}
?>

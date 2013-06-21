<?php
/*
 *    fichier  :  Pronostic.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  24 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Journee.php";
require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Match.php";
require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Equipe.php";
require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Utilisateur.php";

class ctrl_Pronostic extends Controller
{
	public function Load()
	{
		if ($this->GetParam("lbJournee") == "")
			$journee = model_Journee::GetNextJournee();
		else
			$journee = model_Journee::GetJourneeById($this->GetParam("lbJournee"));
		
		if ($journee == null)
		{
			echo "La prochaine journée n'a pas été préparée.";
			exit;
		}
		
		$aMatch = $journee->ListeMatch();
		$aJournee = model_Journee::Liste();
		$utilisateur = model_Utilisateur::GetUtilisateurById(UserInfo::get('UserId'));
		$aProno = $utilisateur->ListePronostic($journee->Id);
		$journeeUti = $utilisateur->GetJourneeUtilisateur($journee->Id);
		$iEtatFilRouge = $utilisateur->EtatFilRouge($journee);
		
		
		$this->AddData("EtatFilRouge", $iEtatFilRouge);
		$this->AddData("Journee", $journee);
		$this->AddData("ListeMatch", $aMatch);
		$this->AddData("ListeJournee", $aJournee);
		$this->AddData("ListeProno", $aProno);
		$this->AddData("JourneeUtilisateur", $journeeUti);
	}
	
	public function btValider_Click()
	{
		$iJournee = $this->GetParam("lbJournee");
		
		$user = model_Utilisateur::GetUtilisateurById(UserInfo::get('UserId'));
		
		//On supprime les pronostics déjà saisis
		$user->DeletePronosticsJournee($iJournee);
		
		//On récupère la liste des matchs de la journée
		$aMatch = model_Match::Liste($iJournee);
		foreach ($aMatch as $match)
		{
			$sScore = $this->GetParam("tbScore_".$match->Id);
			if ($sScore != "")
			{
				list($iScoreEquipe, $iScoreVisiteur) = model_Pronostic::ExplodeScore($sScore);
				
				$match->ScoreEquipe = $iScoreEquipe;
				$match->ScoreVisiteur = $iScoreVisiteur;
				$user->Pronostic($match);
			}
		}
		
		$bFilRouge = ($this->GetParam("cbFilRouge") == "on") ? true : false;
		
		$user->Joue($iJournee, $bFilRouge, false);
	}
	
	public function lbJournee_Change()
	{
		
	}
}
?>

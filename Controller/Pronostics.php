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

class ctrl_Pronostics extends Controller
{
	var $_Mode = "";
	
	public function Load()
	{
		if ($this->GetParam("lbJournee") == "")
		{
			if (UserInfo::get('Administrateur'))
				$journee = model_Journee::GetLastJourneePronosticsAdmin();
			else
				$journee = model_Journee::GetLastJourneePronostics();
			
			if ($journee == "")
			{
				echo "Début de saison, aucun pronostic disponible.";
				UserInfo::un_set('Module');
				exit;
			}
		}
		else
		{
			$journee = model_Journee::GetJourneeById($this->GetParam("lbJournee"));
		}

		if ($journee->DiffuserPronostics && $this->_Mode == "")
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
			foreach ($aMatch as $match)
			{
				$match->LoadStatistiques();
			}
		}
		
		if (UserInfo::get('Administrateur'))
			$aJournee = model_Journee::Liste();
		else
			$aJournee = $journee->ListeJourneePronostics();
		
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
	
	public function lbValider_Click()
	{
		$iJournee = $this->GetParam("lbJournee");
		
		$aUser = model_Utilisateur::Liste();
		
		//On récupère la liste des matchs de la journée
		$aMatch = model_Match::Liste($iJournee);
		foreach ($aUser as $user)
		{
			//On supprime les pronostics du joueur
			$user->DeletePronosticsJournee($iJournee);
			
			$bFilRouge = ($this->GetParam("cbFilRouge_".$user->Id) == "on") ? true : false;
			$bGrilleAveugle = ($this->GetParam("cbAveugle_".$user->Id) == "on") ? true : false;
			$user->Joue($iJournee, $bFilRouge, $bGrilleAveugle);
		
			foreach ($aMatch as $match)
			{
				if ($this->GetParam("tbProno_".$user->Id."_".$match->Id) != "")
				{
					$iScoreEquipe = substr($this->GetParam("tbProno_".$user->Id."_".$match->Id), 0, 1);
					$iScoreVisiteur = substr($this->GetParam("tbProno_".$user->Id."_".$match->Id), -1);
		
					$match->ScoreEquipe = $iScoreEquipe;
					$match->ScoreVisiteur = $iScoreVisiteur;
					$user->Pronostic($match);
				}
			}
		}
		
		//Calcul des risques
		model_Journee::CalculRisque($iJournee);
	}
	
	public function cbAveugle_Change()
	{
		//On récupère la grille aveugle de l'utilisateur connecté
		$utilisateur = model_Utilisateur::GetUtilisateurById($this->GetParam("IdUtilisateur"));
		$aGrille = $utilisateur->GetGrilleAveugle();
		
		$sFlux = "<grille>";
		foreach ($aGrille as $grille)
			$sFlux .= "<score>".$grille["ScoreEquipe"].$grille["ScoreVisiteur"]."</score>";
			
		$sFlux .= "</grille>";
		
		header('Content-Type: text/xml'); 
		echo "<?xml version=\"1.0\"?>\n";
		echo $sFlux;
		exit;
	}
	
	public function lbJournee_Change()
	{
		
	}
}
?>

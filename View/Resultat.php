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

require_once "BlocJournee.php";

class view_Resultat extends View
{
	var $_Resultat = array();
	var $_Journee = null;
	var $_ListeMatch = array();
	var $_Mode = "";
	var $_ListeJournee = array();
	
	function getTable()
	{
		$sBlocDiffuser = "";
		$sBlocResultat = "";
		$sSuffixeTemplate = "";
		$sClassLigne = "lignePaire";
		
		if ($this->_Mode == "ADM")
		{
			$sSuffixeTemplate = "_admin";
			$sBlocDiffuser = "<a id=\"lbDiffuser\" href=\"javascript:Valid('lbDiffuser_Click', 'IdDiffuser=".$this->_Journee->Id."&Type=resultats')\">Diffuser les résultats</a><br><a id=\"lbCalculer\" href=\"javascript:Valid('lbCalculer_Click', 'IdJournee=".$this->_Journee->Id."')\">Calculer les points</a>";
		}
		
		$sLigneMatch = "";
		foreach ($this->_ListeMatch as $match)
		{
			$tp = new Template();
			$tp->addTag("TAG-CHP NomEquipe", $match->Equipe->Nom);
			$tp->addTag("TAG-CHP NomVisiteur", $match->Visiteur->Nom);
			$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Resultat_cellule_match.htm");
			$sLigneMatch .= $tp->getContenu();
			
			$sScore = "";
			if ($match->ScoreEquipe != "" && $match->ScoreVisiteur != "")
				$sScore = $match->ScoreEquipe." - ".$match->ScoreVisiteur;
			
			$tp = new Template();
			$tp->addTag("TAG-CHP IdMatch", $match->Id);
			$tp->addTag("TAG-CHP Resultat", $sScore);
			$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Resultat_cellule_resultat".$sSuffixeTemplate.".htm");
			$sBlocResultat .= $tp->getContenu();
		}
		
		$sLigneUtilisateur = "";
		foreach ($this->_Resultat as $row)
		{
			$utilisateur = $row["Utilisateur"];
			$prono = $row["Prono"];
			$journeeUti = $row["JourneeUtilisateur"];
			$sCelluleProno = "";
			
			foreach ($this->_ListeMatch as $match)
			{
				$iPronoEquipe = "";
				$iPronoVisiteur = "";
				$sType = "";
				$sClass = "normal";
				if (array_key_exists($match->Id, $prono))
				{
					$iPronoEquipe = $prono[$match->Id]->PronoEquipe;
					$iPronoVisiteur = $prono[$match->Id]->PronoVisiteur;
					
					if ($match->ScoreEquipe != "" && $match->ScoreVisiteur != "")
					{
						$sRisque = $prono[$match->Id]->Risque;
						$iRisque = $prono[$match->Id]->Risque;
						
						$iDifScore = $match->ScoreEquipe - $match->ScoreVisiteur;
						$iDifProno = $iPronoEquipe - $iPronoVisiteur;
						
						if ($iPronoEquipe == $match->ScoreEquipe && $iPronoVisiteur == $match->ScoreVisiteur)
							$sClass = "BonScore";
						elseif ($iDifScore > 0 && $iDifProno > 0)
							$sClass = "BonResultat";
						elseif ($iDifScore == 0 && $iDifProno == 0)
							$sClass = "BonResultat";
						elseif ($iDifScore < 0 && $iDifProno < 0)
							$sClass = "BonResultat";
					}
				}
				
				$tp = new Template();
				$tp->addTag("TAG-CHP Class", $sClass);
				$tp->addTag("TAG-CHP PronoEquipe", $iPronoEquipe);
				$tp->addTag("TAG-CHP PronoVisiteur", $iPronoVisiteur);
				$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Resultat_cellule.htm");
				$sCelluleProno .= $tp->getContenu();
			}
			
			$sPoints = ($journeeUti->Points == "") ? "&nbsp;" : $journeeUti->Points;
			$sBonus = ($journeeUti->Bonus == "") ? "&nbsp;" : $journeeUti->Bonus;
			$sMalus = ($journeeUti->Malus == "") ? "&nbsp;" : $journeeUti->Malus;
			
			switch ($row["EtatFilRouge"])
			{
				case -1 :
					$sEtatFilRouge = "Joué";
					break;
				case 0 :
					$sEtatFilRouge = "<font color=blue>Non</font>";
					break;
				case 1 :
					$sEtatFilRouge = "<font color=\"#FF0000\">OUI</font>";
					break;
			}
			
			$sClassLigne = ($sClassLigne == "lignePaire") ? "ligneImpaire" : "lignePaire";
			
			$tp = new Template();
			$tp->addTag("TAG-CHP Class", $sClassLigne);
			$tp->addTag("TAG-CHP Nom", $utilisateur->Nom);
			$tp->addTag("TAG-CHP Prenom", $utilisateur->Prenom);
			$tp->addTag("TAG-BLC Prono", $sCelluleProno);
			$tp->addTag("TAG-CHP EtatFilRouge", $sEtatFilRouge);
			$tp->addTag("TAG-CHP Points", $sPoints);
			$tp->addTag("TAG-CHP Bonus", $sBonus);
			$tp->addTag("TAG-CHP Malus", $sMalus);
			$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Resultat_ligne.htm");
			$sLigneUtilisateur .= $tp->getContenu();
		}
		
		/*while (list($IdMatch, $Compteur) = each($aRisques))
		{
			$indiceRisque1 = 1;
			$indiceRisque2 = 1;
			$indiceRisque3 = 1;
			$indiceRisque4 = 1;
			while (list($Type, $Nombre) = each($Compteur))
			{
				$sClass = "";
				if ($Nombre < 2)
				{
					$sClass = "risque1".$indiceRisque1;
					$indiceRisque1++;
				}
				else if ($Nombre < 4)
				{
					$sClass = "risque2".$indiceRisque2;
					$indiceRisque2++;
				}
				else if ($Nombre < 6)
				{
					$sClass = "risque3".$indiceRisque3;
					$indiceRisque3++;
				}
				else if ($Nombre < 8)
				{
					$sClass = "risque4".$indiceRisque4;
					$indiceRisque4++;
				}
				$sLigneUtilisateur = str_replace("<!--TAG-CHP Match_".$IdMatch."_".$Type."-->", $sClass, $sLigneUtilisateur);
			}
			
		}*/
		
		$tp = new Template();
		$tp->addTag("TAG-BLC Resultat", $sBlocResultat);
		$tp->addTag("TAG-BLC LigneMatch", $sLigneMatch);
		$tp->addTag("TAG-BLC LigneUtilisateur", $sLigneUtilisateur);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Resultat_table.htm");
		return $tp->getContenu();
	}
	
	function Display()
	{
		$viewBlocJournee = new view_BlocJournee();
		$viewBlocJournee->_Journee = $this->_Journee;
		$viewBlocJournee->_ListeJournee = $this->_ListeJournee;
		$sBlocJournee = $viewBlocJournee->Genere();
		
		$sBlocDiffuser = "";
		if ($this->_Mode == "ADM" && !$this->_Journee->DiffuserResultats)
		{
			$sBlocDiffuser = "<a id=\"lbDiffuser\" href=\"javascript:Valid('lbDiffuser_Click', 'IdDiffuser=".$this->_Journee->Id."&Type=resultats')\">Diffuser les résultats</a><br><a id=\"lbCalculer\" href=\"javascript:Valid('lbCalculer_Click', 'IdJournee=".$this->_Journee->Id."')\">Calculer les points</a>";
		}
		
		if ($this->_Journee->DiffuserResultats)
		{
			$sTable = file_get_contents($_SERVER['DOCUMENT_ROOT'].cst_TableJournee."/Resultats_".$this->_Journee->Id.".txt");
		}
		else
		{
			$sTable = $this->getTable();
		}
		
		$tp = new Template();
		$tp->addTag("TAG-BLC Journee", $sBlocJournee);
		$tp->addTag("TAG-BLC BlocDiffuser", $sBlocDiffuser);		
		$tp->addTag("TAG-BLC Table", $sTable);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Resultat.htm");
		$tp->affiche();
	}
}
?>

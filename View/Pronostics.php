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

class view_Pronostics extends View
{
	var $_Resultat = array();
	var $_Journee = null;
	var $_ListeMatch = array();
	var $_Mode = "";
	var $_ListeJournee = array();
	
	function getTable()
	{
		$sBlocResultat = "";
		$sSuffixeTemplate = "";
		$sClass = "lignePaire";

		$sLigneMatch = "";
		$sVictoire = '';
		$sNul = '';
		$sDefaite = '';

		foreach ($this->_ListeMatch as $match)
		{
			$tp = new Template();
			$tp->addTag("TAG-CHP NomEquipe", $match->Equipe->Nom);
			$tp->addTag("TAG-CHP NomVisiteur", $match->Visiteur->Nom);
			$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Resultat_cellule_match.htm");
			$sLigneMatch .= $tp->getContenu();

			$sClassRisque = ($match->Statistiques['victoire']['nombre'] == 0) ? 'normal' : 'risque'.$match->Statistiques['victoire']['risque'].'1';

			$tp = new Template();
			$tp->addTag("TAG-CHP nb", $match->Statistiques['victoire']['nombre']);
			$tp->addTag("TAG-CHP Class", $sClassRisque);
			$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Pronostics_cellule_statistique.htm");
			$sVictoire .= $tp->getContenu();

			$sClassRisque = ($match->Statistiques['nul']['nombre'] == 0) ? 'normal' : 'risque'.$match->Statistiques['nul']['risque'].'1';

			$tp = new Template();
			$tp->addTag("TAG-CHP nb", $match->Statistiques['nul']['nombre']);
			$tp->addTag("TAG-CHP Class", $sClassRisque);
			$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Pronostics_cellule_statistique.htm");
			$sNul .= $tp->getContenu();

			$sClassRisque = ($match->Statistiques['defaite']['nombre'] == 0) ? 'normal' : 'risque'.$match->Statistiques['defaite']['risque'].'1';

			$tp = new Template();
			$tp->addTag("TAG-CHP nb", $match->Statistiques['defaite']['nombre']);
			$tp->addTag("TAG-CHP Class", $sClassRisque);
			$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Pronostics_cellule_statistique.htm");
			$sDefaite .= $tp->getContenu();

			//On initialise le tableau des risques
			$aRisques[$match->Id] = array();
		}
		
		$sSuffixeTemplate = ($this->_Mode == "ADM") ? "_admin" : "";
		
		$sLigneUtilisateur = "";
		foreach ($this->_Resultat as $row)
		{
			$utilisateur = $row["Utilisateur"];
			$prono = $row["Prono"];
			$journeeUti = $row["JourneeUtilisateur"];
			$sCelluleProno = "";
			
			$bAveugle = $journeeUti->GrilleAveugle;
			
			foreach ($this->_ListeMatch as $match)
			{
				$sPronoEquipe = "";
				$sPronoVisiteur = "";
				$sType = "";
				$sClassRisque = "normal";
				if (array_key_exists($match->Id, $prono))
				{
					$sPronoEquipe = $prono[$match->Id]->PronoEquipe;
					$sPronoVisiteur = $prono[$match->Id]->PronoVisiteur;
					$iRisque = $prono[$match->Id]->Risque;
					
					if ($iRisque == 0)
					{
						$sClassRisque = ($bAveugle) ? "risqueAveugle" : "normal";
					}
					else
					{
						if ($sPronoEquipe > $sPronoVisiteur)
							$sType = "V"; 
						else if ($sPronoEquipe == $sPronoVisiteur)
							$sType = "N";
						else
							$sType = "D";
						
						if (array_key_exists($iRisque, $aRisques[$match->Id]))
						{
							if ($aRisques[$match->Id][$iRisque] == $sType)
								$sClassRisque = "risque".$iRisque."1";
							else
								$sClassRisque = "risque".$iRisque."2";
						}
						else
						{
							$sClassRisque = "risque".$iRisque."1";
							$aRisques[$match->Id][$iRisque] = $sType;
						}
					}
				}
				
				$sProno = "";
				if ($sPronoEquipe != "" && $sPronoVisiteur != "")
				{
					$sProno = $sPronoEquipe." - ".$sPronoVisiteur;
				}
				
				$tp = new Template();
				$tp->addTag("TAG-CHP Class", $sClassRisque);
				$tp->addTag("TAG-CHP Prono", $sProno);
				$tp->addTag("TAG-CHP IdUtilisateur", $utilisateur->Id);
				$tp->addTag("TAG-CHP IdMatch", $match->Id);
				$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Pronostics_cellule".$sSuffixeTemplate.".htm");
				$sCelluleProno .= $tp->getContenu();
			}
			
			$sCheckedAveugle = ($journeeUti->GrilleAveugle) ? " checked" : "";
			$sCheckedFilRouge = ($journeeUti->FilRouge) ? " checked" : "";
			
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
			
			$sClass = ($sClass == "lignePaire") ? "ligneImpaire" : "lignePaire";
			
			$tp = new Template();
			$tp->addTag("TAG-CHP Class", $sClass);
			$tp->addTag("TAG-CHP Nom", $utilisateur->Nom);
			$tp->addTag("TAG-CHP Prenom", $utilisateur->Prenom);
			$tp->addTag("TAG-BLC Prono", $sCelluleProno);
			$tp->addTag("TAG-CHP Id", $utilisateur->Id);
			$tp->addTag("TAG-CHP checkedAveugle", $sCheckedAveugle);
			$tp->addTag("TAG-CHP checkedFilRouge", $sCheckedFilRouge);
			$tp->addTag("TAG-CHP EtatFilRouge", $sEtatFilRouge);
			$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Pronostics_ligne".$sSuffixeTemplate.".htm");
			$sLigneUtilisateur .= $tp->getContenu();
		}
		
		$tp = new Template();
		$tp->addTag("TAG-BLC LigneMatch", $sLigneMatch);
		$tp->addTag("TAG-BLC LigneUtilisateur", $sLigneUtilisateur);
		$tp->addTag("TAG-BLC Victoire", $sVictoire);
		$tp->addTag("TAG-BLC Nul", $sNul);
		$tp->addTag("TAG-BLC Defaite", $sDefaite);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Pronostics_table".$sSuffixeTemplate.".htm");
		return $tp->getContenu();
	}
	
	function Display()
	{
		$viewBlocJournee = new view_BlocJournee();
		$viewBlocJournee->_Journee = $this->_Journee;
		$viewBlocJournee->_ListeJournee = $this->_ListeJournee;
		
		$sBlocJournee = $viewBlocJournee->Genere();
		
		$sSuffixeTemplate = ($this->_Mode == "ADM") ? "_admin" : "";
		
		if ($this->_Mode == "ADM" && !$this->_Journee->DiffuserPronostics)
		{
			$sSuffixeTemplate = "_admin";
			$sBlocDiffuser = Template::GetLink("Diffuser les pronostics", "javascript:Valid('lbDiffuser_Click', 'IdDiffuser=".$this->_Journee->Id."&Type=pronostics')")."<br>";
			$sBlocDiffuser .= "<a href=\"javascript:Valid('lbValider_Click')\">Valider</a>";
		}
		else
		{
			$sBlocDiffuser = "";
		}
		
		if ($this->_Journee->DiffuserPronostics)
		{
			$sTable = file_get_contents($_SERVER['DOCUMENT_ROOT'].cst_TableJournee."/Pronostics_".$this->_Journee->Id.".txt");
		}
		else
		{
			$sTable = $this->getTable();
		}
		
		$tp = new Template();
		$tp->addTag("TAG-BLC Journee", $sBlocJournee);
		$tp->addTag("TAG-BLC BlocDiffuser", $sBlocDiffuser);
		$tp->addTag("TAG-BLC Table", $sTable);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Pronostics".$sSuffixeTemplate.".htm");
		$tp->affiche();
	}
}
?>

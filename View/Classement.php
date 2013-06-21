<?php
/*
 *    fichier  :  Classement.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  30 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require_once "BlocJournee.php";

class view_Classement extends View
{
	var $_ListeUtilisateur = array();
	var $_Journee = null;
	var $_Mode = "";
	
	function getTable()
	{
		$sLigneUtilisateur = "";
		$sClass = "lignePaire";
		foreach ($this->_ListeUtilisateur as $utilisateur)
		{
			$iBonus = $utilisateur->Bonus;
			$iMalus = $utilisateur->Malus;
			$iDifference = $iBonus - $iMalus;
			$iMouvement = $utilisateur->Mouvement;
			if ($iMouvement > 0)
				$sMouvement = "<font color=green>".$iMouvement."</font>";
			elseif ($iMouvement == 0)
				$sMouvement = $iMouvement;
			else
				$sMouvement = "<font color=red>".$iMouvement."</font>";
			
			$sClass = ($sClass == "lignePaire") ? "ligneImpaire" : "lignePaire";
			
			$sEtatFilRouge = "&nbsp;";
			switch ($utilisateur->EtatFilRouge)
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
			
			$sPseudo = $utilisateur->Pseudo;
			if ($sPseudo == "")
				$sPseudo = $utilisateur->Nom."&nbsp;".$utilisateur->Prenom;
			
			$tp = new Template();
			$tp->addTag("TAG-CHP Class", $sClass);
			$tp->addTag("TAG-CHP Classement", $utilisateur->Classement);
			$tp->addTag("TAG-CHP Nom", $utilisateur->Nom);
			$tp->addTag("TAG-CHP Prenom", $utilisateur->Prenom);
			$tp->addTag("TAG-CHP Pseudo", $sPseudo);
			$tp->addTag("TAG-CHP Points", $utilisateur->Points);
			$tp->addTag("TAG-CHP Bonus", $iBonus);
			$tp->addTag("TAG-CHP Malus", $iMalus);
			$tp->addTag("TAG-CHP Difference", $iDifference);
			$tp->addTag("TAG-CHP Mouvement", $sMouvement);
			$tp->addTag("TAG-CHP EtatFilRouge", $sEtatFilRouge);
			$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Classement_ligne.htm");
			$sLigneUtilisateur .= $tp->getContenu();
		}
		
		$tp = new Template();
		$tp->addTag("TAG-BLC LigneUtilisateur", $sLigneUtilisateur);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Classement_table.htm");
		return $tp->getContenu();
	}
	
	function Display()
	{
		$viewBlocJournee = new view_BlocJournee();
		$viewBlocJournee->_Journee = $this->_Journee;
		$sBlocJournee = $viewBlocJournee->Genere();

		if ($this->_Mode == "")
		{
			$sTable = file_get_contents($_SERVER['DOCUMENT_ROOT'].cst_TableJournee."/Classement.txt");
		}
		else
		{
			$sTable = $this->getTable();
		}
	
		$tp = new Template();
		$tp->addTag("TAG-BLC TableClassement", $sTable);
		$tp->addTag("TAG-CHP BlocJournee", $sBlocJournee);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Classement.htm");
		$tp->affiche();
	}
}
?>
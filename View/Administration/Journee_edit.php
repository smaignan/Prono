<?php
/*
 *    fichier  :  Journee_edit.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  23 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

class view_Journee_edit extends View
{
	var $_Journee = null;
	var $_ListeMatch = array();
	var $_ListeEquipe = array();
	var $_Mode = "";
	var $_Id = "";

	function Display()
	{
		$sNumero = "";
		$sMatchRetour = "";
		$sDiffuserPronostics = "";
		$sDiffuserResultats = "";
		$sDateFin = "";
		$sDateJournee = "";
		$sBoutonSupprimer = "";
		$sLigneAjoutMatch = "";
		$sLigneMatch = "";
		$sLienAjouter = "";
		$sMessage = "";
		
		if ($this->_Id == "")
		{
			if ($this->_Journee != "")
			{
				$sNumero = $this->_Journee->Numero + 1;
				if ($this->_Journee->MatchRetour)
					$sMatchRetour = " checked";
			}
		}
		else
		{
			$sLienAjouter = Template::GetLink("Ajouter un match", "javascript:Valid('lbAjouter_Click')");
			$sNumero = $this->_Journee->Numero;
			$sMatchRetour = ($this->_Journee->MatchRetour) ? " checked" : "";
			$sDiffuserPronostics = ($this->_Journee->DiffuserPronostics) ? " checked" : "";
			$sDiffuserResultats = ($this->_Journee->DiffuserResultats) ? " checked" : "";
			$sDateFin = date("d/m/Y H:i", $this->_Journee->DateFin);
			$sDateJournee = date("d/m/Y H:i", $this->_Journee->DateJournee);
			$sBoutonSupprimer = Template::GetButton("btSupprimer", "Supprimer", "ValidSup('btSupprimer_Click')");
			
			foreach ($this->_ListeMatch as $match)
			{
				$tp = new Template();
				$tp->addTag("TAG-CHP Id", $match->Id);
				$tp->addTag("TAG-CHP NomEquipe", $match->Equipe->Nom);
				$tp->addTag("TAG-CHP NomVisiteur", $match->Visiteur->Nom);
				$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Administration/Match_ligne.htm");
				$sLigneMatch .= $tp->getContenu();
			}
			
			if ($this->_Mode == "ADD")
			{
				$sLigneEquipe = "";
				foreach ($this->_ListeEquipe as $equipe)
					$sLigneEquipe .= Template::GetOption($equipe->Nom, $equipe->Id); 

				if ($sLigneEquipe == "")
				{
					$sMessage = "Toutes les rencontres sont créées.";
				}
				else
				{
					$tp = new Template();
					$tp->addTag("TAG-BLC OptionEquipe", $sLigneEquipe);
					$tp->addTag("TAG-CHP IdJournee", $this->_Journee->Id);
					$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Administration/Match_edit.htm");
					$sLigneAjoutMatch = $tp->getContenu();
				}
			}
		}
			
		$tp = new Template();
		$tp->addTag("TAG-CHP Numero", $sNumero);
		$tp->addTag("TAG-CHP MatchRetour", $sMatchRetour);
		$tp->addTag("TAG-CHP DiffuserPronostics", $sDiffuserPronostics);
		$tp->addTag("TAG-CHP DiffuserResultats", $sDiffuserResultats);
		$tp->addTag("TAG-CHP DateFin", $sDateFin);
		$tp->addTag("TAG-CHP DateJournee", $sDateJournee);
		$tp->addTag("TAG-CHP Message", $sMessage);
		$tp->addTag("TAG-BLC LigneAjoutMatch", $sLigneAjoutMatch);
		$tp->addTag("TAG-BLC LienAjout", $sLienAjouter);
		$tp->addTag("TAG-BLC BoutonSupprimer", $sBoutonSupprimer);
		$tp->addTag("TAG-BLC LigneMatch", $sLigneMatch);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Administration/Journee_edit.htm");
		$tp->affiche();
	}	
}
?>

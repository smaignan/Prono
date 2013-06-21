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

class view_Pronostic extends View
{
	var $_Journee = null;
	var $_ListeMatch = array();
	var $_ListeJournee = array();
	var $_ListeProno = array();
	var $_JourneeUtilisateur = null;
	var $_EtatFilRouge = false;
	
	function Display()
	{
		$sSuffixe = ($this->_Journee->DateFin < time()) ? "_consult" : "";
		
		$sDate = date("d/m/Y", $this->_Journee->DateJournee);
		
		$sOptionJournee = "";
		foreach ($this->_ListeJournee as $journee)
			$sOptionJournee .= Template::GetOption(Template::GetNumero($journee->Numero)." journée", $journee->Id, ($journee->Id == $this->_Journee->Id));
		
		$sLigneMatch = "";
		$sClass = "lignePaire";
		
		$sCheckedFilRouge = ($this->_JourneeUtilisateur->FilRouge) ? " checked" : "";
		
		if ($sSuffixe == "_consult" || $this->_EtatFilRouge != 0)
			$sCheckedFilRouge .= " disabled";
		
		foreach ($this->_ListeMatch as $match)
		{
			$sProno = "";
			if (array_key_exists($match->Id, $this->_ListeProno))
			{
				$sPronoEquipe = $this->_ListeProno[$match->Id]->PronoEquipe;
				$sPronoVisiteur = $this->_ListeProno[$match->Id]->PronoVisiteur;

				$sProno = $sPronoEquipe." - ".$sPronoVisiteur; 
			}
			
			if ($sSuffixe == "_consult" && $sProno == "")
				$sProno = "&nbsp;";
			
			$sClass = ($sClass == "lignePaire") ? "ligneImpaire" : "lignePaire";

			$tp = new Template();
			$tp->addTag("TAG-CHP Id", $match->Id);
			$tp->addTag("TAG-CHP NomEquipe", $match->Equipe->Nom);
			$tp->addTag("TAG-CHP NomVisiteur", $match->Visiteur->Nom);
			$tp->addTag("TAG-CHP Score", $sProno);
			$tp->addTag("TAG-CHP Class", $sClass);
			$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Match_ligne$sSuffixe.htm");
			$sLigneMatch .= $tp->getContenu();
		}
	
		$tp = new Template();
		$tp->addTag("TAG-CHP FilRouge", $sCheckedFilRouge);
		$tp->addTag("TAG-CHP Date", $sDate);
		$tp->addTag("TAG-BLC LigneMatch", $sLigneMatch);
		$tp->addTag("TAG-BLC OptionJournee", $sOptionJournee);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Pronostic$sSuffixe.htm");
		$tp->affiche();
	}
}
?>

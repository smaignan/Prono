<?php
/*
 *    fichier  :  Journee_lst.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  11 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

class view_Journee_lst extends View
{
	var $_ListeJournee = array();
	
	function Display()
	{
		$sLigneJournee = "";
		foreach($this->_ListeJournee as $journee)
		{
			$tp = new Template();
			$tp->addTag("TAG-CHP Id",$journee->Id);
			$tp->addTag("TAG-CHP Numero",$journee->Numero);
			$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Administration/Journee_ligne.htm");
			$sLigneJournee .= $tp->getContenu();
		}
		
		$tp = new Template();
		$tp->addTag("TAG-BLC listeJournee", $sLigneJournee);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Administration/Journee_lst.htm");
		$tp->affiche();
	}	
}
?>
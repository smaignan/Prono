<?php
/*
 *    fichier  :  Utilisateur_lst.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  11 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

class view_Utilisateur_lst extends View
{
	var $_ListeUtilisateur = array();
	var $_IdEdit = 0;
	var $_Mode = "";
	
	function Display()
	{
		$sLigneUtilisateur = "";
		foreach($this->_ListeUtilisateur as $utilisateur)
		{
			$tp = new Template();
			$tp->addTag("TAG-CHP Nom",$utilisateur->Nom);
			$tp->addTag("TAG-CHP Id",$utilisateur->Id);
			$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Administration/Utilisateur_ligne.htm");
			$sLigneUtilisateur .= $tp->getContenu();
		}
		
		$tp = new Template();
		$tp->addTag("TAG-BLC listeUtilisateur", $sLigneUtilisateur);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Administration/Utilisateur_lst.htm");
		$tp->affiche();
	}	
}
?>
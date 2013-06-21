<?php
/*
 *    fichier  :  Equipe_lst.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  14 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

class view_Equipe_lst extends View
{
	var $_ListeEquipe = array();
	var $_IdEdit = 0;
	var $_Mode = "";

	function Display()
	{
		$sBlocAjout = "";
		if ($this->_Mode == "ADD")
		{
			$tp = new Template();
			$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Administration/Equipe_add.htm");
			$sBlocAjout = $tp->getContenu();
		}
		
		$sLigneEquipe = "";
		foreach($this->_ListeEquipe as $equipe)
		{
			$tp = new Template();
			$tp->addTag("TAG-CHP Nom",$equipe->Nom);
			$tp->addTag("TAG-CHP Id",$equipe->Id);
			if ($this->_IdEdit == $equipe->Id)
				$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Administration/Equipe_edit.htm");
			else
				$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Administration/Equipe_ligne.htm");
			$sLigneEquipe .= $tp->getContenu();
		}
		
		$tp = new Template();
		$tp->addTag("TAG-BLC listeEquipe", $sLigneEquipe);
		$tp->addTag("TAG-BLC Ajout", $sBlocAjout);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Administration/Equipe_lst.htm");
		$tp->affiche();
	}	
}
?>

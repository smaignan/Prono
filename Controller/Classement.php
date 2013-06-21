<?php
/*
 *    fichier  :  Classement.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  16 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Utilisateur.php";

class ctrl_Classement extends Controller
{
	var $_Mode = "";

	public function Load()
	{
		$journee = model_Journee::GetLastJourneeResultats();

		if ($journee != "")
		{
			if ($this->_Mode == "VIEW")
				$aUtilisateur = model_Utilisateur::Classement($journee);
			else
				$aUtilisateur = array();
			
			$this->AddData("Mode", $this->_Mode);
			$this->AddData("ListeUtilisateur", $aUtilisateur);
			$this->AddData("Journee", $journee);
		}
	}
}
?>
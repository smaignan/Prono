<?php
/*
 *    fichier  :  Login.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  23 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

class view_Login extends View
{
	var $_ErreurAuthentification = 0;
	
	function Display()
	{
		$sMessage = "";
		
		if ($this->_ErreurAuthentification == 1)
			$sMessage = "Paramètre de connexion incorrect";
		
		$tp = new Template();
		$tp->addTag("TAG-CHP Message", $sMessage);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Login.htm");
		$tp->affiche();
	}
}
?>

<?php
/*
 *    fichier  :  Utilisateur_edit.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  16 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

class view_Utilisateur_edit extends View
{
	var $_Utilisateur = null;

	function Display()
	{
		$id = "";
		$sNom = "";
		$sPrenom = "";
		$sPseudo = "";
		$sMail = "";
		$sMail2 = "";
		$sLogin = "";
		$sPassword = "";
		$iBonus = 0;
		$iMalus = 0;
		$sAdmin = "";
		$sBoutonSupprimer = "";
		
		if ($this->_Utilisateur != null)
		{
			$id = $this->_Utilisateur->Id;
			$sNom = $this->_Utilisateur->Nom;
			$sPrenom = $this->_Utilisateur->Prenom;
			$sPseudo = $this->_Utilisateur->Pseudo;
			$sMail = $this->_Utilisateur->Mail;
			$sMail2 = $this->_Utilisateur->Mail2;
			$sLogin = $this->_Utilisateur->Login;
			$sPassword = $this->_Utilisateur->Password;
			$iBonus = $this->_Utilisateur->Bonus;
			$iMalus = $this->_Utilisateur->Malus;
			$sAdmin = ($this->_Utilisateur->Admin) ? " checked" : "";
			$sBoutonSupprimer = Template::GetButton("btSupprimer", "Supprimer", "ValidSup('btSupprimer_Click')");
		}
			
		$tp = new Template();
		$tp->addTag("TAG-CHP Id", $id);
		$tp->addTag("TAG-CHP Nom", $sNom);
		$tp->addTag("TAG-CHP Prenom", $sPrenom);
		$tp->addTag("TAG-CHP Pseudo", $sPseudo);
		$tp->addTag("TAG-CHP Mail", $sMail);
		$tp->addTag("TAG-CHP Mail2", $sMail2);
		$tp->addTag("TAG-CHP Login", $sLogin);
		$tp->addTag("TAG-CHP Password", $sPassword);
		$tp->addTag("TAG-CHP Bonus", $iBonus);
		$tp->addTag("TAG-CHP Malus", $iMalus);
		$tp->addTag("TAG-CHP Admin", $sAdmin);
		$tp->addTag("TAG-BLC BoutonSupprimer", $sBoutonSupprimer);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Administration/Utilisateur_edit.htm");
		$tp->affiche();
	}	
}
?>

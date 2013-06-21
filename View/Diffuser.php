<?php
/*
 *    fichier  :  Diffuser.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  15 févr. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

class view_Diffuser extends View
{
	var $_IdJournee;
	var $_Type;
	
	function Display()
	{
		$sTitre = "";
		$sLien = "";
		
		switch ($this->_Type)
		{
			case "pronostics" :
				$sTitre = "Diffusion des pronostics";
				$sLien = Template::GetLink("pronostics", "http://".$_SERVER["HTTP_HOST"].cst_Racine."/?Module=pronostics&lbJournee=".$this->_IdJournee);
				break;
			case "resultats" :
				$sTitre = "Diffusion les résultats";
				$sLien = Template::GetLink("résultats", "http://".$_SERVER["HTTP_HOST"].cst_Racine."/?Module=resultat&lbJournee=".$this->_IdJournee);
				break;
		}
		
		$tp = new Template();
		$tp->addTag("TAG-CHP Lien", $sLien);
		$tp->addTag("TAG-CHP Titre", $sTitre);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Diffuser.htm");
		$tp->affiche();
	}
}
?>

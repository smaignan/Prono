<?php
/*
 *    fichier  :  Compte.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  6 févr. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

class view_Compte extends View
{
	var $_GrilleAveugle = array();
	var $_NombreEquipe = 0;
	var $_Password = "";
	
	function Display()
	{
		$sLigne = "";
		$sClass = "lignePaire";
		
		for ($i=0; $i<$this->_NombreEquipe; $i++)
		{
			$sScore = "";
			if (array_key_exists($i, $this->_GrilleAveugle))
			{
				$sScoreEquipe = $this->_GrilleAveugle[$i]["ScoreEquipe"];
				$sScoreVisiteur = $this->_GrilleAveugle[$i]["ScoreVisiteur"];
				if ($sScoreEquipe != "" && $sScoreVisiteur != "")
					$sScore = $sScoreEquipe." - ".$sScoreVisiteur;
			}
			
			$sClass = ($sClass == "lignePaire") ? "ligneImpaire" : "lignePaire";
			
			$tp = new Template();
			$tp->addTag("TAG-CHP IdGrille", $i);
			$tp->addTag("TAG-CHP Score", $sScore);
			$tp->addTag("TAG-CHP Class", $sClass);
			$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Compte_ligne.htm");
			$sLigne .= $tp->getContenu();
		}
		
		$tp = new Template();
		$tp->addTag("TAG-CHP Password", $this->_Password);
		$tp->addTag("TAG-BLC Ligne", $sLigne);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Compte.htm");
		$tp->affiche();
	}
}
?>

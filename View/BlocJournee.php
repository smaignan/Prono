<?php
/*
 *    fichier  :  BlocJournee.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  5 f�vr. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

class view_BlocJournee extends View
{
	var $_Journee = null;
	var $_ListeJournee = array();
	
	function Genere()
	{
		if ($this->_Journee == "")
		{
			return "";
		}
		else
		{
			$sListeJournee = "";
			foreach ($this->_ListeJournee as $journee)
			{
				$sIndice = ($journee->Numero == 1) ? "er" : "�me";
				$sListeJournee .= Template::GetOption($journee->Numero.$sIndice." journ�e", $journee->Id, ($journee->Id == $this->_Journee->Id));
			}
			
			if ($sListeJournee == "")
			{
				$sListeJournee = "<u>".Template::GetNumero($this->_Journee->Numero)." journ�e</u>";
			}
			else
			{
				$sListeJournee = "<select name=\"lbJournee\" onChange=\"javascript:Valid('lbJournee_Change')\">".$sListeJournee."</select>";
			}
			
			$sDate = date("d/m/Y", $this->_Journee->DateFin);
	
			$tp = new Template();
			$tp->addTag("TAG-BLC ListeJournee", $sListeJournee);
			$tp->addTag("TAG-CHP Date", $sDate);
			$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/BlocJournee.htm");
			return $tp->getContenu();
		}
	}
}
?>

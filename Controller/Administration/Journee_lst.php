<?php
/*
 *    fichier  :  Journee_lst.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  23 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Journee.php";

class ctrl_Journee_lst extends Controller
{
	public function Load()
	{
		$this->AddData("ListeJournee", model_Journee::Liste());
	}
	
	public function lbValidSup_Click()
	{
		$journee = model_Journee::GetJourneeById($this->GetParam("Id"));
		$journee->Delete();
	}
}
?>

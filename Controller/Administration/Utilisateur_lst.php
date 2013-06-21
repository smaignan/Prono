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

require $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Utilisateur.php";

class ctrl_Utilisateur_lst extends Controller
{
	public function Load()
	{
		$this->AddData("ListeUtilisateur", model_Utilisateur::Liste());
		$this->AddData("IdEdit", $this->GetParam("Id"));
		$this->AddData("Mode", $this->GetParam("Mode"));
	}

	public function lbValidSup_Click()
	{
		$utilisateur = model_Utilisateur::GetUtilisateurById($this->GetParam("Id"));
		$utilisateur->Delete();
	}
}
?>

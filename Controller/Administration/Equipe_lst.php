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

require $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Equipe.php";

class ctrl_Equipe_lst extends Controller
{
	public function Load()
	{		
		$this->AddData("ListeEquipe", model_Equipe::Liste());
		$this->AddData("IdEdit", $this->GetParam("Id"));
		$this->AddData("Mode", $this->GetParam("Mode"));
	}
	
	public function lbValidModif_Click()
	{
		$equipe = model_Equipe::GetEquipeById($this->GetParam("Id"));
		$equipe->Nom = $this->GetParam("Nom");
		$equipe->Update();
		Controller::Redirect("equipe");
	}
	
	public function lbValidSup_Click()
	{
		$equipe = model_Equipe::GetEquipeById($this->GetParam("Id"));
		$equipe->Delete();
		Controller::Redirect("equipe");
	}
	
	public function lbValidAjout_Click()
	{
		$equipe = new model_Equipe();
		$equipe->Nom = $this->GetParam("Nom");
		$equipe->Add();
		Controller::Redirect("equipe");
	}
}
?>

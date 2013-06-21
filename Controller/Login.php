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

require $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Utilisateur.php";

class ctrl_Login extends Controller
{
	public function Load()
	{
		if ($_SERVER["REQUEST_METHOD"] == "GET")
			UserInfo::destroy();
			//session_destroy();
	}
	
	public function btValider_Click()
	{
		$utilisateur = new model_Utilisateur();
		$utilisateur->Login = $this->GetParam("tbLogin");
		$utilisateur->Password = $this->GetParam("tbPassword");
		
		if ($utilisateur->Authentifie())
		{
			//$_SESSION["UserId"] = $utilisateur->Id;
			//$_SESSION["Administrateur"] = $utilisateur->Admin;
			UserInfo::set('UserId', $utilisateur->Id);
			UserInfo::set('Administrateur', $utilisateur->Admin);

			Controller::Redirect("classement");
		}
		else
		{
			$this->AddData("ErreurAuthentification", 1);
		}
	}
}
?>

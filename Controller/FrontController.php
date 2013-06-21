<?php
/*
 *    fichier  :  FrontController.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  16 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require "Controller/Controller.php";
require "Controller/Request.php";
require "View/View.php";
require "Common/UserInfo.php";

class FrontController
{
	var $view = null;
	var $ModuleTemp = "";

	public function Load()
	{
		if ($this->ModuleTemp == "")
		{
			if (!UserInfo::is_set('Module'))
				UserInfo::set('Module', 'classement');

			if (!UserInfo::is_set('Administrateur'))
				UserInfo::set('Administrateur', false);
			
			if (isset($_GET["Module"]))
			{
				UserInfo::set('Module', $_GET["Module"]);

				if ($_GET["Module"] == "logout")
				{
					UserInfo::destroy();
					header("Location:.");
				}
			}
			
			$moduleDemande = UserInfo::get('Module');
		}
		else
		{
			$moduleDemande = $this->ModuleTemp;
		}
				
		$dom = new DomDocument();
		$dom->load("Config/Action.xml");
		$xpath = new DOMXPath($dom);
		$root = $dom->documentElement;
		$modules = $xpath->evaluate('Module[@id="'.$moduleDemande.'"]', $root);
		if ($modules->length == 0)
		{
			echo "Le module demandé n'existe pas";
			return false;
		}
		else if ($modules->length > 1)
		{
			echo "Module en double";
			return false;
		}
		else
		{
			$module = $modules->item(0);
			
			//Vérification de la sécurité
			//Connexion à l'application
			if ($module->getElementsByTagName("Protected")->length == 1)
			{
				//C'est un module protégé
				//On vérifie que l'utilisateur c'est connecté.
				if (!UserInfo::is_set('UserId'))
				{
					UserInfo::destroy();
					Controller::Redirect("login");
				}
				
				//Accès à l'administration
				$nodesTypeAction = $module->GetElementsByTagName("TypeAction");
				if ($nodesTypeAction->length == 1)
				{
					if ($nodesTypeAction->item(0)->nodeValue == "ADMINISTRATION")
					{
						if (!UserInfo::get('Administrateur'))
						{
							Controller::Redirect("classement");
						}
					}
				}
			}
		}
		
		$sName = $module->getElementsByTagName("Name")->item(0)->nodeValue;
		$sControllerPath = $module->getElementsByTagName("ControllerPath")->item(0)->nodeValue;
		$sControllerClassName = "ctrl_$sName";
		
		//Objet Request
		$request = new Request();
	
		require("Controller/".$sControllerPath);
		$controller = new $sControllerClassName();
		$controller->setRequest($request);
		
		if ($_SERVER["REQUEST_METHOD"] == "POST" && $this->ModuleTemp == "")
		{
			$sCommandName = $_POST["Command"]; 
			$controller->$sCommandName();
		}
		
		if ($this->ModuleTemp != "")
			$controller->_Mode = "VIEW";
		
		$controller->Load();
		
		$sName = $module->getElementsByTagName("Name")->item(0)->nodeValue;
		$sViewPath = $module->getElementsByTagName("ViewPath")->item(0)->nodeValue;
		if ($sViewPath != "")
		{
			$sViewClassName = "view_$sName";
		
			//On charge la vue
			require("View/".$sViewPath);
			$this->view = new $sViewClassName();
			$this->view->setParameters($controller->GetData());
		}
		
		return true;
	}
	
	public function Display()
	{
		if ($this->view != null)
			$this->view->Display();
	}
}
?>

<?php
/*
 *    fichier  :  Menu.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  15 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

class Menu
{
	var $root = null;
	
	public function __construct()
	{
		$dom = new DomDocument();
		if (UserInfo::is_set('UserId'))
			$dom->load("Config/Menu.xml");
		else
			$dom->load("Config/MenuPublic.xml");
		
		$this->root = $dom->documentElement;
	}
	
	public function GetLigneMenu($node, $indice)
	{
		$sLien = ($node->hasAttribute("Module")) ? "?Module=".$node->getAttribute("Module") : "";
		$sLibelle = utf8_decode($node->getAttribute("Name"));
		$sBlocSousMenu = ($node->hasChildNodes()) ? $this->GetBlocSousMenu($node, $indice) : "";
		
		$tp = new Template();
		$tp->addTag("TAG-CHP Lien",$sLien);
		$tp->addTag("TAG-CHP Libelle",$sLibelle);
		$tp->addTag("TAG-BLC SousMenu",$sBlocSousMenu);
		$tp->addTag("TAG-CHP Indice",$indice);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Menu/LigneMenu.htm");
		return $tp->getContenu();
	}
	
	public function GetBlocSousMenu($node, $indice)
	{
		$sLigneSousMenu = "";
		foreach($node->childNodes as $child)
			if ($child->nodeType == XML_ELEMENT_NODE) $sLigneSousMenu .= $this->GetLigneSousMenu($child);
		
		$tp = new Template();
		$tp->addTag("TAG-BLC SousMenu",$sLigneSousMenu);
		$tp->addTag("TAG-CHP Indice",$indice);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Menu/BlocSousMenu.htm");
		return $tp->getContenu();
	}
	
	public function GetLigneSousMenu($node)
	{
		$sLienSousMenu = Template::GetLink(utf8_decode($node->getAttribute("Name")), "?Module=".$node->getAttribute("Module"));

		$tp = new Template();
		$tp->addTag("TAG-BLC Lien",$sLienSousMenu);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Menu/LigneSousMenu.htm");
		return $tp->getContenu();
	}
	
	public function Display()
	{
		$sLigneMenu = "";

		$listNode = $this->root->getElementsByTagName("Menu");
		$indice = 0;
		foreach ($listNode as $node)
		{
			$bAcces = true;
			if ($node->hasAttribute("Type"))
			{
				if ($node->getAttribute("Type") == "ADM")
					$bAcces = UserInfo::get('Administrateur');
			}
				
			if ($bAcces)
			{
				$indice++;
				$sLigneMenu .= $this->GetLigneMenu($node, $indice);
			}
		}
		
		$tp = new Template();
		$tp->addTag("TAG-BLC Menu",$sLigneMenu);
		$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Menu/Menu.htm");
		$tp->affiche();
	}
}
?>

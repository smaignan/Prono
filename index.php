<?php
/*
 *    fichier  :  Index.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  16 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require "Config.php";
require "Technique/Template.php";
require "Technique/Menu.php";
require "Controller/FrontController.php";
require "BDD/StoredProcedure.php";
require "BDD/Connection.php";
require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/BDD/SGBD.php";

session_start();
header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
		
$controller = new FrontController();

if ($controller->Load())
{
	$tp = new Template();
	$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Page/Header.htm");
	$tp->affiche();
	
	$menu = new Menu();
	$menu->Display();
		
	$controller->Display();
	
	$tp = new Template();
	$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Page/Footer.htm");
	$tp->affiche();
}
?>
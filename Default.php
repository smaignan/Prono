<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<?php
/*
 * Created on 8 janv. 08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
require "Config.php";
require "Technique/template.php";
require "Model/Utilisateur.php";
require "Model/Equipe.php";

/*$ListeUtilisateurs = Utilisateur::Liste();
foreach($ListeUtilisateurs as $utilisateur)
	echo "Nom : ".$utilisateur->Nom."<br>";

echo "<HR>";
$ListeEquipes = Equipe::Liste();
foreach($ListeEquipes as $equipe)
	echo "Nom : ".$equipe->Nom."<br>";
	


 $tp = new Template();
//$tp->addTag("TAG-CHP contexte",$s_contexte);
$tp->setFichier($_SERVER['DOCUMENT_ROOT'].cst_Template."/Default.htm");
$tp->affiche();
*/



 ?>
 <html>
 <head>
<title>Menu déroulant horizontal</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<script type="text/javascript">
<!--

}
//-->
</script>


<style type="text/css">
<!-- 

-->
</style>
</head>
<body>
<?php
echo $sMenu;
?>
</body>
</html>

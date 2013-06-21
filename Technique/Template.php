<?php
class Template {
	function __construct() {
		$this->a_TagVal = array();
	}
	
	public function setFichier($s_Fichier) {
		$this->fichier = str_replace("\\\\","\\",$s_Fichier);
		$this->fichier = str_replace("/","\\",$this->fichier);
		$this->fichier = str_replace("\\","/",$this->fichier);
		if (!file_exists($this->fichier)) {
			echo "Fichier introuvable : ".$this->fichier."<br>";
			exit();
		}
	}
	
	public function getContenu() {
		$this->fusion();
		return $this->contenu;
	}
	
	public function affiche() {
		$this->fusion();
		echo $this->contenu;
	}
	
	public function addTag($s_Tag, $s_Val) {
		$this->a_TagVal[$s_Tag] = $s_Val;
	}
	
	private function fusion() {
		$id_file = fopen($this->fichier,"r");
		$i_TailleFichier = filesize($this->fichier);
		$this->contenu = fread($id_file,$i_TailleFichier);
		if (is_array($this->a_TagVal)) {
			while (list($cle, $val) = each($this->a_TagVal)) {
				$this->contenu = str_replace("<!--$cle-->",$val,$this->contenu);
			}
		}
		fclose($id_file);
	}
	
	public static function GetNumero($Numero)
	{
		return ($Numero == 1) ? $Numero."er" : $Numero."ème"; 
	}
	
	public static function GetLink($Name, $Link)
	{
		return "<a href=\"$Link\">$Name</a>";
	}
	
	public static function GetButton($Name, $Text, $Action)
	{
		return "<input type=\"button\" value=\"$Text\" id=\"$Name\" onClick=\"$Action\">";
	}
	
	public static function GetOption($Text, $Value)
	{
		$sSelected = "";

		if (func_num_args() == 3)
			$sSelected = (func_get_arg(2)) ? " selected" : "";

		return "<option value=\"$Value\"$sSelected>$Text</option>";
	}
}
?>
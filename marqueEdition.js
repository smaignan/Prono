/**
* afficheRenommeMarque()
* Construit un input text à la place du libelle de la marque (ajout methode sur onkeypress et onblur)
* @param  indice : indice de la ligne cliqué
* @param  tabMarqueName : marques ou marques_ombrelles
**/
function afficheRenommeMarque(indice,tabMarqueName,indiceMarque){

	// on recupere l'ancienne marque
	var oldName = $('spanDescr'+indice).innerHTML;	

	var reg1=new RegExp('"', 'g');
	oldName = oldName.replace(reg1,'&#34;');
	
	// On supprime le texte
	$('spanDescr'+indice).innerHTML = "";	
	// onajoute à la place un input text avec la valeur "oldname"
	Element.update('spanDescr'+indice,'<input id="edit'+indice+'" type="text" value="'+ oldName +'" class="txt0" onkeypress="onEnter(event,'+indice+');">');
	// on ajoute un onblur à l'input créé, puis on lui donne  le focus
	$('edit'+indice).onblur = function() { renommeMarqueAjax(oldName, indice, tabMarqueName,indiceMarque) };
	$('edit'+indice).focus();

}


/**
* onEnter()
* Fction appelé sur le onkeypress, appel fct renommeMarqueAjax definit sur le onblur
* @param  event : keyboard event
* @param  indice : indice de la ligne
**/
function onEnter(event,indice){

	// Code du enter
	if(event.keyCode==13){
		// Appel de renommeMarqueAjax
		$('edit'+indice).blur();
	}
}

/**
* renommeMarqueAjax()
* Fct permettant la maj du nom d'une marque
*@param oldName : ancien nom
* @param newName : nouveau nom
* @param tabMarqueName  :  marques ou marques_ombrelles
**/
function renommeMarqueAjax(oldName, indice, tabMarqueName, indiceMarque){
	
	$('messRetour').innerHTML = "";	
	
	newName = $('edit'+indice).value;
	
	var reg1=new RegExp('<', 'g');
	var reg2=new RegExp('>', 'g');
	newName = newName.replace(reg1,'&lt;');
	newName = newName.replace(reg2,'&gt;');

	if(oldName != newName){
	
		$('waitRespAjax').style.display = "inline";
		
		new Ajax.Request( '/marque/renommerMarqueAjax.php', {
			method: 'post',
			parameters: {'oldName': oldName,'newName' : newName,'tabMarque' : tabMarqueName,'indiceMarque':indiceMarque},
			onSuccess: function(xhr) {
					// On recupere le nouveau nom
					newName = xhr.responseXML.getElementsByTagName('newName')[0].childNodes[0].nodeValue;
					// On supprime l'input
					Element.remove($('edit'+indice));
					// Pour retrouver \'  et \"
					var reg1=new RegExp("\\\\'", "g");
					var reg2=new RegExp('\\\\"', 'g');
					// Pour retrouver \\
					var reg3=new RegExp('\\\\\\\\', 'g');
					newName = newName.replace(reg1,"&apos;");
					newName = newName.replace(reg2,'&quot;');
					newName = newName.replace(reg3,'\\');
					
					var r=new RegExp('&apos;', 'g');
					newName = newName.replace(r,"'");
					
					// on remet le nouveau nom 
					$('spanDescr'+indice).innerHTML = newName;
					
					$('messRetour').innerHTML = oldName + BIEN_ETE_RENOMME + newName;				

					$('waitRespAjax').style.display = "none";
			},

			onFailure: function(xhr) {
					alert(ERREUR_AJAX);
			}
		});
	}else{
		$('spanDescr'+indice).innerHTML = newName;
	}
}


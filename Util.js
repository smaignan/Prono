function Valid(action)
{
	sParam = "";
	for (i=1; i<arguments.length; i++)
		sParam = arguments[i] + "&";

	if (sParam != "")
	{
		sParam = sParam.substring(0, sParam.length - 1);
		objParam = document.getElementById("Param")
		objParam.value = sParam;
	}
	
	objAction = document.getElementById("Command")
	objAction.value = action;

	document.FORMNAME.submit()
}

var s_classe = "";
function f_onFocus(objText) {
	s_classe = objText.className
	objText.className = "onFocus"
}
function f_onBlur(objText) {
	objText.className = s_classe
}

var s_classeScore = "";
function ScoreFocus(objText)
{
	s_classeScore = objText.className
	objText.className = s_classeScore + "_onFocus"

	sValue = objText.value
	if (sValue != "")
	{
		aScore = sValue.split(' - ');
		if (aScore.length == 2)
		{
			sScoreEquipe = aScore[0]
			sScoreVisiteur = aScore[1]
			
			if (isNaN(sScoreEquipe) || isNaN(sScoreVisiteur))
			{
				alert("Format du score incorrect")
			}
			else
			{
				objText.value = sScoreEquipe + sScoreVisiteur;
				objText.select()
			}
		} 
	}
}

function ScoreBlur(objText)
{
	objText.className = s_classeScore

	sValue = objText.value
	if (sValue != "")
	{
		if (isNaN(sValue) || sValue.length != 2)
		{
			alert("Format du score incorrect");
			objText.value = ""
		}
		else
		{
			objText.value = sValue.substring(0, 1) + " - " + sValue.substring(1, 2);
		}
	}
}

function CheckScoreAveugle(objText)
{
	sValue = objText.value
	if (sValue != "")
	{
		aScore = sValue.split(' - ');
		if (aScore.length == 2)
		{
			sScoreEquipe = aScore[0]
			sScoreVisiteur = aScore[1]

			if (!isNaN(sScoreEquipe) && !isNaN(sScoreVisiteur))
			{
				iScoreEquipe = Math.floor(sScoreEquipe)
				iScoreVisiteur = Math.floor(sScoreVisiteur)

				if (iScoreEquipe + iScoreVisiteur < 5)
				{
					alert("La somme des scores doit-être égale ou supérieure à 5.")
					objText.value = ""; 
				}
			}
		}
	}
}

var ClassNameLigneOver;

function ChangeClass(row)
{
	var sClassName = ClassNameLigneOver;
	var regSelected = /_Selected/;
	if (regSelected.test(sClassName))
	{
		row.className = sClassName.replace(regSelected, "");
		ClassNameLigneOver = row.className 
	}
	else
	{
		row.className = sClassName + "_Selected"
	}
}

function Ligne_Over(ligne)
{
	ClassNameLigneOver = ligne.className;
	ligne.className = "ligne_Over";
}

function Ligne_Out(ligne)
{
	var sClassName = ligne.className;
	var regSelected = /_Selected/;
	if (!regSelected.test(sClassName))
	{
		ligne.className = ClassNameLigneOver;
	}
}

function Ligne_Click(ligne)
{
	ChangeClass(ligne)
}
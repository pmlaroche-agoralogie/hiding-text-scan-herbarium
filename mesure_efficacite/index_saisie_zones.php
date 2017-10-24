<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>iHerbarium</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
<body>
<?php
	
$erreur = '';
if (isset($_GET['retour'])){
	if ($_GET['retour']==1){
		$erreur = "<strong>Nom de répertoire incorrect.</strong>";	
	}else{
		if ($_GET['retour']==2){
			$erreur = "<strong>Aucun fichier dans ce répertoire.</strong>";	
		}else{
			if ($_GET['retour']==3){
				$erreur = "<strong>Aucune image valide dans ce répertoire.</strong>";	
			}
		}
	}
}
	
affiche_formulaire($erreur);
	
	 
function affiche_formulaire($erreur){
	echo '<fieldset>'."\n";
	echo '<p>'.$erreur.'</p>'."\n";
	echo '<form action="saisie_zones.php" method="POST" name="form_image" id="form_image" >'."\n";
	echo '<p>Saisissez le nom du répertoire des images :</p>'."\n";
	echo '<input type="hidden" name="action" id="action" value="upload" />'."\n";
	echo '<p><input type="text" name="repertoire" id="repertoire" /></p>'."\n";	
	echo '<p><input type="submit" name="button" id="button" value="Valider" /></p>'."\n";
	echo '</form>'."\n";
	echo '</fieldset>'."\n";
}
	   

?>
</body>
</html>




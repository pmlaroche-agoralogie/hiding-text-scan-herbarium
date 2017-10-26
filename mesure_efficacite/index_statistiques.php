<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>iHerbarium</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php include("header.php");?>
<?php
	
	echo '<fieldset>'."\n";
	echo '<form action="statistiques.php" method="POST" name="form_image" id="form_image" >'."\n";
	echo '<p>Saisissez le nom du répertoire des images originales :</p>'."\n";
	echo '<p><input type="text" name="repertoire_original" id="repertoire_original" /></p>'."\n";	
	echo '<p>Saisissez le nom du répertoire des images avec les zones blanches :</p>'."\n";
	echo '<p><input type="text" name="repertoire_blanc" id="repertoire_blanc" /></p>'."\n";	
	echo '<input type="hidden" name="action" id="action" value="calcul" />'."\n";
	echo '<p><input type="submit" name="button" id="button" value="Valider" /></p>'."\n";
	echo '</form>'."\n";
	echo '</fieldset>'."\n";

?>
</body>
</html>




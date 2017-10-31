<?php 
session_start(); 
require "functions.php";

include("header.php");
        
	$result_calcul = '';
	if (isset($_POST['action'])){			
		if ($_POST['action'] == 'calcul' && isset($_POST['repertoire_original']) && isset($_POST['repertoire_blanc'])) {
			$result_calcul = calcul_statistiques($_POST['repertoire_original'],$_POST['repertoire_blanc']);
		}
		
		?>
		<div id="wrapper">
			<?php echo $result_calcul; ?>
		</div>
		<?php 
	}else{
		?>
		
		<fieldset>
		<form action="statistiques.php" method="POST" name="form_image" id="form_image" >
		<p>Saisissez le nom du répertoire des images originales :</p>
		<p><input type="text" name="repertoire_original" id="repertoire_original" value="imagesrecues"/></p>
		<p>Saisissez le nom du répertoire des images avec les zones blanches :</p>
		<p><input type="text" name="repertoire_blanc" id="repertoire_blanc" value="images_blanco"/></p>
		<p>Saisissez le nom du répertoire des fichiers hocr :</p>
		<p><input type="text" name="repertoire_hocr" id="repertoire_hocr" value="hocr"/></p>
		<input type="hidden" name="action" id="action" value="calcul" />
		<p><input type="submit" name="button" id="button" value="Valider" /></p>
		</form>
		</fieldset>
		
	<?php 		
	}
	
include("footer.php");

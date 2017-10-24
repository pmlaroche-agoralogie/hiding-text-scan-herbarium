<?php

$erreur = '';

if (isset($_POST['action'])){

	if ($_POST['action']=='upload'){

		if ($_FILES['vdocument']['name'] != "" && filesize($_FILES['vdocument']['tmp_name']) > 0){	
		
			$document = $_FILES['vdocument'];
		
			$lefichier_temp = $document['tmp_name'];
		
			if(is_uploaded_file($lefichier_temp)){
			
				$lefichier_nom = $document['name'];		
				$lefichier_nom = str_fichier($lefichier_nom);

				$content_dir = 'imagesrecues/';
				
				if(move_uploaded_file($lefichier_temp, $content_dir.$lefichier_nom)){
					file_put_contents('atraiter.txt', $lefichier_nom);
					$erreur = "<p>L'image <strong>".$lefichier_nom."</strong> a &eacute;t&eacute; upload&eacute;e dans le dossier <strong>imagesrecues.</strong></p>";
					$erreur .= "<p>Le fichier <strong>atraiter.txt</strong> a &eacute;t&eacute; g&eacute;n&eacute;r&eacute;.</p>";
				}else{
					$erreur = "<strong>Impossible d'uploader l'image</strong>";				
				}
			}else{
				$erreur = "<strong>Impossible d'uploader l'image</strong>";				
			}
		}else{
			$erreur = "<strong>L'image est vide</strong>";
		}
		
	}	
				
}

affiche_formulaire($erreur);
	
	 
function affiche_formulaire($erreur){
	echo '<fieldset>'."\n";
	echo '<p>'.$erreur.'</p>'."\n";
	echo '<form action="" method="POST" enctype="multipart/form-data" name="form_image" id="form_image" >'."\n";
	echo '<input type="hidden" name="action" id="action" value="upload" />'."\n";
	echo '<p><input name="vdocument" type="file" id="vdocument" value="" /></p>'."\n";
	echo '<p><input type="submit" name="button" id="button" value="Ajouter cette image" /></p>'."\n";
	echo '</form>'."\n";
	echo '</fieldset>'."\n";
}
	   
function str_fichier($text) {


	$text = str_replace(" ","",$text);
	$text = str_replace("'","",$text);
	$text = str_replace('"',"",$text);
	$text = str_replace('-',"_",$text);
 
  // Lettre accentuées
  $tofind = "ÀÁÂÃÄÅàáâãäåÇçÒÓÔÕÖØòóôõöøÈÉÊËèéêëÌÍÎÏìíîïÙÚÛÜùúûü¾ÝÿýÑñ";
 
  // Equivalent non accentué
  $replac = "AAAAAAaaaaaaCcOOOOOOooooooEEEEeeeeIIIIiiiiUUUUuuuuYYyyNn"; 
 
  // Suppression des lettres accentuées par leur équivalent
  $text = strtr($text,utf8_decode($tofind),$replac);


	$tab_autorise = array();
	foreach(range('A','Z') as $i) {
		$tab_autorise[] = $i;
	}
	foreach(range('a','z') as $i) {
		$tab_autorise[] = $i;
	}
	for ($i=0; $i<10; $i++){
		$tab_autorise[] = strval($i);
	}

	$tab_autorise[] = '_';
	$tab_autorise[] = '.';

	$new_texte = '';
	for ($z=0; $z<strlen($text); $z++){
		$car = substr($text,$z,1);
		if (in_array($car, $tab_autorise, true)) {	
			$new_texte .= $car;
		}
	
	}

 
  return $new_texte;
}


         
?>
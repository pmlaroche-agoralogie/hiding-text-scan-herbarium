<?php
include ("connexion-data.php");

define("MIN_ZONE_HOCR",2);
define("MAX_ZONE_HOCR",20);

function init_variables($repertoire){

	$erreur = '';
	if ($repertoire != ''){	
		if (substr($repertoire,strlen($repertoire)-1,1) != '/'){
			$repertoire = $repertoire."/";
		}
		$files = scandir($repertoire);	
		if (count($files) > 0){
			$tab_toutes_images = array();
			for ($i=0; $i<count($files); $i++){
				if ($files[$i] != '.' && $files[$i] != '..'){
					if (strpos($files[$i],".") !== false){								
						$tab_toutes_images[] = $files[$i];
					}
				}
			}
			if (count($tab_toutes_images) > 0){
				$tab_images = array();
				for ($i=0; $i<count($tab_toutes_images); $i++){
					$fichier_image = $tab_toutes_images[$i];
					$pos_point = strrpos($fichier_image, ".");
					$pos_blanc = strpos($fichier_image, "_blanc");
					if ($pos_point !== false && $pos_blanc === false){ // on vérifie qu'il y a bien un point dans le nom et qu'il n'y a pas "blanc" dans le nom
						$tab_images[] = $fichier_image;
					}
				}
				if (count($tab_images) > 0){
					$_SESSION['repertoire'] = $repertoire;
					$_SESSION['tab_images'] = $tab_images;
					$_SESSION['num_image'] = 0;
				}else{
					$erreur = "<strong>Nom de répertoire incorrect.</strong>";
				}
			}else{
				$erreur = "<strong>Nom de répertoire incorrect.</strong>";
			}
		}else{
			$erreur = "<strong>Aucun fichier dans ce répertoire.</strong>";
		}
	}else{
		$erreur = "<strong>Nom de répertoire incorrect.</strong>";
	}	
	return $erreur;	
}

function next_image(){

	$_SESSION['num_image'] = $_SESSION['num_image'] + 1;
	
}
function prev_image(){

	$_SESSION['num_image'] = $_SESSION['num_image'] - 1;
	
}
function enregistre_base(){

	$fichier_image = $_POST['fichier_image'];
	$zones_image = $_POST['zones_image'];

	$connect_db = se_connecter();
	$requetesql = 'SELECT * FROM images_verif WHERE nom = "'.$fichier_image.'"';
	$resultat = mysql_query($requetesql,$connect_db);

	if (!$resultat) {
	    die('Requête invalide : ' . mysql_error());
	}
	
	if(mysql_num_rows($resultat)==1){
		$row = mysql_fetch_assoc($resultat);
		$requetesql_update = "UPDATE images_verif SET zones = '".$zones_image."', resultat = -1, datetest = 0 WHERE uid = ".$row['uid'];
		$resultat_requete = mysql_query($requetesql_update,$connect_db);
	}else{			
		$requetesql_insert = "INSERT INTO images_verif (nom,zones,resultat,datetest) VALUES ('".$fichier_image."','".$zones_image."',-1,0)";
		$resultat_requete = mysql_query($requetesql_insert,$connect_db);
	}

	return $resultat_requete;
}

function get_nom_image_jpg($id)
{

        $connect_db = se_connecter();
        $requetesql = "SELECT * FROM images_sources WHERE uid = $id";
      $resultat = mysql_query($requetesql,$connect_db);
 if(mysql_num_rows($resultat)==1){
                $row = mysql_fetch_assoc($resultat);
                return ($row['OCCURRENCEID'].'.jpg');

        }
else
return '';
}
function recuperer_image($id)
{

	$connect_db = se_connecter();
	$requetesql = "SELECT * FROM images_sources WHERE uid = $id";
      $resultat = mysql_query($requetesql,$connect_db);
 if(mysql_num_rows($resultat)==1){
                $row = mysql_fetch_assoc($resultat);
                $url = $row['IDENTIFIER'];
		$fichier = file_get_contents($url);
		file_put_contents('imagesrecues/'.$row['OCCURRENCEID'].'.jpg',$fichier);
echo $row['OCCURRENCEID'];

        }
}

function get_zones_image($fichier_image){

	$connect_db = se_connecter();
	$requetesql = 'SELECT zones FROM images_verif WHERE nom = "'.$fichier_image.'"';
	$resultat = mysql_query($requetesql,$connect_db);
	$zones = '';
	if(mysql_num_rows($resultat)==1){
		$row = mysql_fetch_assoc($resultat);
		$zones = $row['zones'];
	}
	return $zones;
	
}


function get_nom_image($uid){
	$nom_image = '';
	$connect_db = se_connecter();
	$requetesql = 'SELECT nom FROM images_verif WHERE uid = '.$uid;
	$resultat = mysql_query($requetesql,$connect_db);
	if(mysql_num_rows($resultat)==1){
		$row = mysql_fetch_assoc($resultat);
		$nom_image = $row['nom'];
	}
	return $nom_image;
}

function calcul_statistiques($repertoire_original,$repertoire_blanc){

	if (substr($repertoire_original,strlen($repertoire_original)-1,1) != '/'){
		$repertoire_original = $repertoire_original."/";
	}
	$_SESSION['repertoire_original'] = $repertoire_original;
	if (substr($repertoire_blanc,strlen($repertoire_blanc)-1,1) != '/'){
		$repertoire_blanc = $repertoire_blanc."/";
	}
	$_SESSION['repertoire_blanc'] = $repertoire_blanc;
				
	$nb_image = 0;
	$nb_traitee = 0;
	$nb_ok = 0;
	$nb_ko = 0;
	$contenu_table = '';
	
	$connect_db = se_connecter();
	$requetesql = 'SELECT * FROM images_verif ORDER BY uid';
	$resultat = mysql_query($requetesql,$connect_db);	

	while($row = mysql_fetch_assoc($resultat)){
		$nb_image++;
		$image_ok = compare_zones_blanches($repertoire_blanc,$row['nom'],$row['zones']);
		if ($image_ok != -1){
			// enregistrement dans la base que si on a pu vérifier avec l'image blanche
			$requetesql_update = "UPDATE images_verif SET resultat = ".$image_ok.", datetest = ".time()." WHERE uid = ".$row['uid'];
			$resultat_requete = mysql_query($requetesql_update,$connect_db);
			$nb_traitee++;
		}
		if ($image_ok==-1){
			$result_calcul = 'Image avec zones blanches non trouvée.';
			$lien_compare = '';
		}else{
			$lien_compare = '<a href="compare.php?uid='.$row['uid'].'" target="_blank">Comparer</a>';
			if ($image_ok==0){
				$nb_ko++;
				$result_calcul = '<span style="color:#ff1503;">Zones sélectionnées NON blanches.</span>';
			}else{
				if ($image_ok==1){
					$nb_ok++;
					$result_calcul = '<span style="color:#09af09;">Zones sélectionnées blanches.</span>';
				}
			}
		}
		
		$nbZonesHocr =  nbZonesHocr($_POST['repertoire_hocr'].'/'.$row['nom'].'_.hocr');
		if ($nbZonesHocr == null)
			$resultNbZonesHocr = '<span style="color:#ff1503;"><span style="text-decoration: underline;">/!\</span> Pas de fichier hocr</span>';		
		elseif ($nbZonesHocr < MIN_ZONE_HOCR)
			$resultNbZonesHocr = $nbZonesHocr.'<br/><span style="color:#ff1503;"><span style="text-decoration: underline;">/!\</span> Nb de zones faible</span>';
		elseif ($nbZonesHocr > MAX_ZONE_HOCR)
			$resultNbZonesHocr = $nbZonesHocr.'<br/><span style="color:#ff1503;"><span style="text-decoration: underline;">/!\</span> Nb de zones important</span>';
		else
			$resultNbZonesHocr = $nbZonesHocr;
		
		$contenu_table .= '<tr><td>'.$row['nom'].'</td><td>'.str_replace(";","<br>",$row['zones']).'</td><td>'.$resultNbZonesHocr.'</td><td>'.$result_calcul.'</td><td>'.$lien_compare.'</td></tr>';
	
	}	
	$retour = '<p>'.$nb_traitee.' images traitées / '.$nb_image.' images dans la base.</p>';
	if ($nb_image > 0){		
		$retour .= '<table class="result">';
		if ($nb_traitee > 0){
			$pourcent_ok = 100 * $nb_ok / $nb_traitee;
			$pourcent_ko = 100 * $nb_ko / $nb_traitee;
		}else{
			$pourcent_ok = 0;
			$pourcent_ko = 0;
		}
		$retour .= '<tr><td><span style="color:#09af09;">OK</span></td><td><span style="color:#ff1503;">KO</span></td></tr>';
		$retour .= '<tr><td><span style="color:#09af09;">'.round($pourcent_ok,2).' %</span></td><td><span style="color:#ff1503;">'.round($pourcent_ko,2).' %</span></td></tr>';
		$retour .= '</table>';
		$retour .= '<table class="result">';
		$retour .= '<tr><td>Image</td><td>Zones saisies</td><td>Nb zones détectées</td><td>Comparaison saisie/zones blanchies</td><td>Visualisation</td></tr>';
		$retour .= $contenu_table;
		$retour .= '</table>';

	}
	return $retour;
	
}

function nbZonesHocr($file)
{
	$nb_zone = null;
	if (file_exists($file)) {
		//$xml = simplexml_load_file($file);
		$doc = new DOMDocument();
		$doc->loadHTMLFile($file);
		//$doc->loadXMLFile($file);
		
		$finder = new DomXPath($doc);
		$classname="ocr_line";
		//$nodes = $finder->query("//*[contains(@class, '$classname')]");
		$nodes = $finder->query("//span[contains(@class, '$classname')]");
		
		$nb_zone = $nodes->length;
		/*echo "<pre>";
		print_r($nodes);
		echo "</pre>";*/
	}
	return $nb_zone;
}




function compare_zones_blanches($repertoire,$fichier_image,$zones_image){
	$image_ok = -1;		
	$pos = strrpos($fichier_image, ".");
	if ($pos !== false){
		$fichier_ss_extension = substr($fichier_image,0,$pos);
		$extension = substr($fichier_image,$pos+1);
		$fichier_blanc = $fichier_ss_extension.'_blanc.'.$extension;
		if (file_exists($repertoire.$fichier_blanc)) {
			$size = getimagesize($repertoire.$fichier_blanc);
			$image_width = $size[0];
			$image_height = $size[1];
			$rapport = $image_width / 1400;
		
			$im = @imagecreatefromjpeg($repertoire.$fichier_blanc);
			$tab_zones = explode(";", $zones_image);
			$image_ok = 1;
			for ($i=0; $i<count($tab_zones); $i++){
				$zone_cour = $tab_zones[$i];
				list($var_x,$var_y,$var_width,$var_height) = explode(",",$zone_cour);
	
				$x_deb = get_integer($var_x * $rapport);
				$x_fin = get_integer(($var_x * $rapport) + ($var_width * $rapport));
				$y_deb = get_integer($var_y * $rapport);
				$y_fin = get_integer(($var_y * $rapport) + ($var_height * $rapport));
				
				for ($x=$x_deb; $x<=$x_fin; $x++){
					for ($y=$y_deb; $y<=$y_fin; $y++){
						$rgb = imagecolorat($im, $x, $y);
						$r = ($rgb >> 16) & 0xFF;
						$g = ($rgb >> 8) & 0xFF;
						$b = $rgb & 0xFF;				
						if ($r >= 252 && $r <= 255 && $g >= 252 && $g <= 255 && $b >= 252 && $b <= 255){
						}else{
							$image_ok = 0;
							break;
						}
					}
					if ($image_ok == 0){
						break;
					}								
				}
				if ($image_ok == 0){
					break;
				}
			}
			imagedestroy($im);
		}
	}
	
	return $image_ok;
}


function get_integer($variable){
	$pos_point = strpos($variable,".");
	if ($pos_point !== false){
		$variable = substr($variable,0,$pos_point);
	}
	return $variable;
}

function se_connecter(){
global $SERVEUR,$USER,$MDP, $BASE;

	// connexion a la BDD
	$connect_db = mysql_connect($SERVEUR,$USER,$MDP); 
	if (!$connect_db) {
 	  die('Impossible de se connecter : ' . mysql_error());
	}
	// selection de la base a utiliser
	$db_selected =	mysql_select_db($BASE,$connect_db); 
	if (!$db_selected) {
	   die ('Impossible de sélectionner la base de données : ' . mysql_error());
	}
	return $connect_db;
}


?>

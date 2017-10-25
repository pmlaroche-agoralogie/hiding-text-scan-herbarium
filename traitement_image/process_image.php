<?php
//header('Content-Type: image/jpeg');


if (isset($_GET['fichier']) && isset($_GET['image'])){
	$name_file = $_GET['fichier'];
	$name_image = $_GET['image'];
	floute_image($name_file,$name_image);
}else{
	echo "<p>Aucun paramètres</p>";
}


function floute_image($name_file,$name_image){
$basedir = '../mesure_efficacite/imagesrecues/';
$basedirhocr= '../mesure_efficacite/hocr/';
$basedirfinal =  '../mesure_efficacite/images_blanco/';
	$name_image = $name_image;
	$name_file = $name_file;
echo "<br>image: ".$name_image;
echo "<br>fichier : ".$name_file;

	$im = LoadJpeg($basedir.$name_image);
	$white = imagecolorallocate($im,255,255,255); 
	
	$doc = new DOMDocument();
	$ok = $doc->loadHTMLFile($basedirhocr.$name_file);
echo "<br>ouverture du fichier : ".$ok;
	
	$searchNode = $doc->getElementsByTagName( "span" ); 
	foreach( $searchNode as $searchNode ) { 
		$valueID = $searchNode->getAttribute('title'); 
		$pos_bbox = strpos($valueID,"bbox");
		$pos_point = strpos($valueID,";");
		if ($pos_point !== false && $pos_bbox !== false) {
			
			$bbox = substr($valueID,$pos_bbox + 5,$pos_point - ($pos_bbox + 5));			
			$tab_bbox = explode(" ", $bbox);
			$largeur = $tab_bbox[2] - $tab_bbox[0];
			$hauteur = $tab_bbox[3] - $tab_bbox[1];
			if (count($tab_bbox)==4 && ( $largeur < 350 ) && ( $hauteur < 300 )){
				imagefilledrectangle ($im ,$tab_bbox[0],$tab_bbox[1],$tab_bbox[2],$tab_bbox[3],$white);
			 }
		 }
	}
	
	$pos_point = strpos($name_image,".");
	$new_name_image = substr($name_image,0,$pos_point).'_blanc.'.substr($name_image,$pos_point+1);
	imagejpeg($im, $basedirfinal.$new_name_image);
	imagedestroy($im);

echo "<br>FIN du traitement.";
}


function LoadJpeg($imgname){
    /* Tente d'ouvrir l'image */
    $im = imagecreatefromjpeg($imgname);

    /* Traitement en cas d'échec */
    if(!$im){
echo "<br>ouverture de l'image : 0";
        /* Création d'une image vide */
        $im  = imagecreatetruecolor(150, 30);
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);

        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

        /* On y affiche un message d'erreur */
        imagestring($im, 1, 5, 5, 'Erreur de chargement ' . $imgname, $tc);
    }else{
echo "<br>ouverture de l'image : 1";
	}

    return $im;
}



?>

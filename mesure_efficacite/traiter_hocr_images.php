<?php session_start(); ?>
<?php require "functions.php"; 
$ordre = '';
for($i=1;$i<1000;$i++) 
	{
$nom = get_nom_image_jpg($i);
echo $nom.'<br>';
$ordre .= '/home/hidingzone/htdocs/traitement_image/scripts/convert-nb-puis-tesseract-export-hocr.sh '.$nom."\n";
}
echo "fin";
file_put_contents('ordreshell',$ordre);
?>

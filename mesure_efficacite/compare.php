<?php session_start(); ?>
<?php require "functions.php"; ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>iHerbarium</title>
		<link href="ressources/select_areas.css" media="screen" rel="stylesheet" type="text/css" />
		<script src="ressources/jquery-1.11.3.min.js" type="text/javascript"></script>
		<script src="ressources/jquery.selectareas.js" type="text/javascript"></script>
	</head>
    
<body>

<?php
if (isset($_GET['uid']) && isset($_SESSION['repertoire_original']) && isset($_SESSION['repertoire_blanc'])){
?>

<div id="wrapper">
			<div class="image-left">
            	<?php 
					$repertoire_original = $_SESSION['repertoire_original'];
					$nom_image = get_nom_image($_GET['uid']);
					
						// on récupère dans la base les zones sensbiles
						$zones = get_zones_image($nom_image);
						if ($zones != ''){
							$tab_zones = explode(";",$zones);
						}else{
							$tab_zones = array();
						}
						echo "<img alt='Image principale' id='example' width='700' src='".$repertoire_original.$nom_image."'/>
						
						<script type='text/javascript'>
							$('img#example').selectAreas({
								minSize: [5, 5],
								overlayOpacity: 0,
								outlineOpacity: 0.5,
								allowEdit: false,
								allowMove: false,
								allowResize: false,
								allowSelect: false,
								allowDelete: false,
								width: 700,
								areas: [";
									$zones_javascript = '';
									for ($i=0; $i<count($tab_zones); $i++){
										$zone_cour = $tab_zones[$i];
										$tab_zone_cour = explode(",",$zone_cour);
										$zones_javascript .= ",{x:".($tab_zone_cour[0]/2).",y:".($tab_zone_cour[1]/2).",width:".($tab_zone_cour[2]/2).",height:".($tab_zone_cour[3]/2)."}";
									}
									if ($zones_javascript != ''){
										$zones_javascript = substr($zones_javascript,1);
										echo $zones_javascript;
									}

									
						echo "]
							});
						</script>";
				?>
			</div>
           	<div class="image-right">
            	<?php
				$repertoire_blanc = $_SESSION['repertoire_blanc'];
				$pos = strrpos($nom_image, ".");
				if ($pos !== false){
					$fichier_ss_extension = substr($nom_image,0,$pos);
					$extension = substr($nom_image,$pos+1);
					$fichier_blanc = $fichier_ss_extension.'_blanc.'.$extension;
					echo "<img alt='Image principale' id='example_blanc' width='700' src='".$repertoire_blanc.$fichier_blanc."'/>";
				}
				?>
			</div>
		</div>

<?php
}else{
header('Location: index_statistiques.php'); 		
}
?>

</body>
</html>
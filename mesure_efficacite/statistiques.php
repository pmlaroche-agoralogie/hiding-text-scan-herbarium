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
	</head>

	<body>
        
    <?php
	$result_calcul = '';
	if (isset($_POST['action'])){			
		if ($_POST['action'] == 'calcul' && isset($_POST['repertoire_original']) && isset($_POST['repertoire_blanc'])) {
			$result_calcul = calcul_statistiques($_POST['repertoire_original'],$_POST['repertoire_blanc']);
		}
	}else{
		header('Location: index_statistiques.php'); 		
	}
	?>
		<div id="wrapper">
			<?php echo $result_calcul; ?>
		</div>

	</body>
</html>

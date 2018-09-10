<?php
require(dirname(__FILE__).'/config/config.inc.php');
require_once(_CLASS_DIR_.'Autoload.php');
spl_autoload_register(array(Autoload::getInstance(), 'load'));

$aPageTitle = array(
                'index' => 'Bienvenue.',
                'setImages' => 'Ajouter des images',
                'setResults' => 'Ajouter des résultats',
                'setWhiteResults' => 'Blanchir des images',
                'getAnalysisZone' => 'Analyse des zones',
                'getAnalysisPBZone' => 'Mise en avant des images à potentiels problèmes',
                'getAnalysisZoneDisplay' => 'Simulateur de zone',
                'getResultsCleanDB' => 'Nettoyage des tables résultats (doublons)',
    );

//Objet database
Db::getInstance();

Dispatcher::getInstance()->dispatch();
?>
<html>
<head>
	<meta charset="UTF-8">
	<base href="<?php echo BASE_URL?>">
	<script src="lib/js/jquery/jquery-3.3.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/styles.css">
  
</head>
<body>
<header>
<h2>Menu</h2>
<ul><li><a href="images/set/"><?php echo $aPageTitle['setImages'];?></a></li>
	<li><a href="results/set/"><?php echo $aPageTitle['setResults'];?></a></li>
	<li><a href="results/white/set/"><?php echo $aPageTitle['setWhiteResults'];?></a></li>
	<li><a href="analysis/zone/get/"><?php echo $aPageTitle['getAnalysisZone'];?></a></li>
	<li><a href="analysis/zone/pb/get/"><?php echo $aPageTitle['getAnalysisPBZone'];?></a></li>
	<li><a href="cleandb/results/get/"><?php echo $aPageTitle['getResultsCleanDB'];?></a></li>
</ul>
<hr/>
<h1><?php echo $title;?></h1>
<?php echo $content;?>
</header>
</body>
</html>

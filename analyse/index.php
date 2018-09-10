<?php
require(dirname(__FILE__).'/config/config.inc.php');
require_once(_CLASS_DIR_.'Autoload.php');
spl_autoload_register(array(Autoload::getInstance(), 'load'));

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
<ul><li><a href="images/set/">Ajouter des images</a></li>
	<li><a href="results/set/">Ajouter des résultats</a></li>
	<li><a href="results/white/set/">Blanchir des images</a></li>
	<li><a href="analysis/zone/get/">Analyse des zones</a></li>
	<li><a href="analysis/zone/pb/get/">Mise en avant des images à potentiels problèmes</a></li>
</ul>
<hr/>
<?php echo $content;?>
</header>
</body>
</html>

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
  <base href="<?php echo BASE_URL?>">
  <script src="lib/js/jquery/jquery-3.3.1.min.js"></script>
  
</head>
<body>
<header>
<ul><li><a href="images/set/">Ajouter des images</a></li>
	<li><a href="results/set/">Ajouter des résultats</a></li>
	<li><a href="results/white/set/">Blanchir des images</a></li>
	<li><a href="analysis/zone/get/">Analyse des zones</a></li>
	<li><a href="analysis/zone/pb/get/">Mise en avant des images à potentiels problèmes</a></li>
</ul>
<?php echo $content;?>
</header>
</body>
</html>

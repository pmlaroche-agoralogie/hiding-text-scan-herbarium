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
		<script type="text/javascript">
			// Log the quantity of selections
			function debugQtyAreas (event, id, areas) {
				console.log(areas.length + " areas", arguments);
				infos_zones = areas.length+" zone(s) sélectionnée(s) :";
				var areas = $('img#example').selectAreas('areas');
				$.each(areas, function (id, area) {
					infos_zones += "<br />"+ area.x+','+area.y+','+area.width+','+area.height;	
				});

				$('#infos_zones').html(infos_zones);
			};

			function enregistreZones(){
				var areas = $('img#example').selectAreas('areas');
				//if (areas.length > 0){
					var text = "";
					$.each(areas, function (id, area) {
						text += ";"+ area.x+','+area.y+','+area.width+','+area.height;	
					});
					if (text != ''){
						text = text.substr(1);
					}
					document.getElementById("zones_image").value = text;
					document.forms['form_enregistre'].submit();
				//}
			}
			
		</script>
	</head>

	<body>
<header>
	<img src="../img/logo_recolnat.png" style="height:60px"/>
	<img src="../img/logo_dicen-idf.png" style="height:40px;padding-left: 20px;">
</header>

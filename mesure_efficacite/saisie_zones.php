<?php 
session_start();
require "functions.php"; 

include("header.php");


	$result_enregistre_zones = '';
	if (isset($_POST['action'])){			
		switch ($_POST['action']) {
			case "upload":
				$erreur = init_variables($_POST['repertoire']);
				break;
			case "next_image":
				next_image();
				break;
			case "prev_image":
				prev_image();
				break;
			case "enregistre_zones":
				$resultat_enreg = enregistre_base();
				if ($resultat_enreg){
					$result_enregistre_zones = '<p><span style="color:#09af09;">Les zones sélectionnées ont été enregistrées dans la base.</p>';
				}else{
					$result_enregistre_zones = '<p><span style="color:#ff1503;">Problème d\'enregistrement dans la base.</span></p>';
				}
				break;
		}
	}
	
	if ($erreur != '' || !isset($_POST['action']))
	{
		?>
		<fieldset>
		<?php echo '<p>'.$erreur.'</p>';?>
		<form action="saisie_zones.php" method="POST" name="form_image" id="form_image" >
		<p>Saisissez le nom du répertoire des images :</p>
		<input type="hidden" name="action" id="action" value="upload" />
		<p><input type="text" name="repertoire" id="repertoire" value="imagesrecues"/></p>
		<p><input type="submit" name="button" id="button" value="Valider" /></p>
		</form>
		</fieldset>
		<?php 
	}
	else 
	{
	?>
		
		<div id="wrapper">
		<div class="image-decorator">
		<?php
		$tab_images = $_SESSION['tab_images'];
		$num_image = $_SESSION['num_image'];
		$repertoire = $_SESSION['repertoire'];
		if (count($tab_images)>0){
			// on récupère dans la base les zones sensbiles
			$zones = get_zones_image($tab_images[$num_image]);
			if ($zones != ''){
				$tab_zones = explode(";",$zones);
			}else{
				$tab_zones = array();
			}
			echo "<img alt='Image principale' id='example' width='1400' src='".$repertoire.$tab_images[$num_image]."'/>
		
			<script type='text/javascript'>
			$('img#example').selectAreas({
			minSize: [5, 5],
			onChanged: debugQtyAreas,
			overlayOpacity: 0,
			outlineOpacity: 0.9,
			width: 1400,
			areas: [";
			$zones_javascript = '';
			for ($i=0; $i<count($tab_zones); $i++){
				$zone_cour = $tab_zones[$i];
				$tab_zone_cour = explode(",",$zone_cour);
				$zones_javascript .= ",{x:".$tab_zone_cour[0].",y:".$tab_zone_cour[1].",width:".$tab_zone_cour[2].",height:".$tab_zone_cour[3]."}";
			}
			if ($zones_javascript != ''){
				$zones_javascript = substr($zones_javascript,1);
				echo $zones_javascript;
			}
		
				
			echo "]
		});
		</script>";
		}
		?>
					</div>
		           	<div class="image-buttons">
		            	<?php 
							if (count($tab_images)>0){
							$index_image = $num_image+1;
							echo '<table>
									<tr>
										<td class="actions">'.$result_enregistre_zones.'<p><strong>Image : '.$index_image.' / '.count($tab_images).'</strong></p>
											<p>'.$tab_images[$num_image].'</p>';
											echo '<p id="infos_zones">'.count($tab_zones).' zone(s) sélectionnée(s) : <br />'.str_replace(";","<br />",$zones).'</p>';
											echo '<form action="" method="POST" name="form_enregistre" id="form_enregistre" >
												<input type="hidden" name="action" id="action" value="enregistre_zones" />
												<input type="hidden" name="fichier_image" id="fichier_image" value="'.$tab_images[$num_image].'" />
												<input type="hidden" name="zones_image" id="zones_image" value="" />
												<input type="button" id="btnEnregistre" value="Enregistre zones" class="actionOn" onClick="enregistreZones();" />									
											</form>';
											
									//		<input type="button" id="btnReset" value="Supprimer" class="actionOn" />';
											
										if ($num_image >0){	
											echo '<form action="" method="POST" name="form_prev" id="form_prev" >
												<input type="hidden" name="action" id="action" value="prev_image" />
												<input type="button" id="btnPrev" value="Image precedente" class="actionOn" onClick="document.forms[\'form_prev\'].submit();" />
												</form>';
										}
										if ($num_image < (count($tab_images) - 1)){	
											echo '<form action="" method="POST" name="form_next" id="form_next" >
												<input type="hidden" name="action" id="action" value="next_image" />
												<input type="button" id="btnNext" value="Image suivante" class="actionOn" onClick="document.forms[\'form_next\'].submit();" />
												</form>';
										}
									echo '</td>
										<td>
										<div id="output" class="output"> </div>
									</td>
								</tr>
							</table>';
							}
						?>
					</div>
				</div>
				<?php 
		
		
	}
	
include("footer.php");
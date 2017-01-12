<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();

// Add Javascript
$doc->addStyleSheet("components/com_perso_norlande/media/perso_norlande/css/style.css",'text/css',"screen");
$doc->addStyleSheet("components/com_perso_norlande/media/perso_norlande/css/jquery-ui.min.css",'text/css',"screen");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery-3.1.1.min.js");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery-ui-1.12.1.min.js");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery.blockUI.js");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/detailsperso.js");

?>

  
<?php include(JPATH_COMPONENT . '/includes/menu.php'); ?>
<?php require_once(JPATH_COMPONENT . '/includes/define.php'); ?>


<form action="index.php?view=detailsperso&format=raw&option=com_perso_norlande&task=createPerso" method="post">
<fieldset>  
  <legend align="left">Cr&eacute;ation d&apos;un personnage</legend>
  
<label for="nom_perso">Nom : </label><input id="nom_perso" type="text" name="nom_perso" /><br>
<label for="lignee_perso">Lignee : </label><select id="lignee_perso" name="lignee_perso">
<?php
foreach(Lignees::$lignees as $key=>$lignee) {
	echo '<option value="'.$key.'">'.$lignee.'</option>';
}
?>
</select><br>
<input type="submit" name="button_submit" value="Créer" />
</fieldset>
</form>

<form action="index.php?view=detailsperso&format=raw&option=com_perso_norlande&task=createPerso" method="post">
<fieldset>  
  <legend align="left">S&eacute;lection d&apos;un personnage</legend>
  
<label for="recherche_perso">Nom du personnage : </label><input id="recherche_perso" type="text" name="nom_perso" /><br>
<input type="submit" name="button_submit" value="Sélectionner" />
</fieldset>
</form>



<script type="text/javascript" >

$(function() {
	$('#recherche_perso').autocomplete({
		source : 'index.php?option=com_perso_norlande&task=searchPerso',
		focus: function( event, ui ) {
                  $( "#recherche_perso" ).val( ui.item.label );
                     return false;
               },
		select: function(event, ui) {
			document.location.href="index.php?option=com_perso_norlande&task=selectPerso&perso_id="+ui.item.value;
			return false;
		},
	});
});

</script>











<?php if($this->perso !=NULL) { ?>
<br>
<h3>Personnage : <?php echo htmlspecialchars($this->perso->getNom().' ('.$this->perso->getLignee().')'); ?></h3>

<form id="formPointsCreation">
<fieldset>  
  <legend align="left">Points de Cr&eacute;ation</legend>
<?php 
$xp = $this->perso->getXp();

echo '<label for="pointsCreation">Points de cr&eacute;ation</label>';
echo '<input type="text" id="pointsCreation" name="pointsCreation" class="shortNb" value="'.$xp->getPointsCreation().'"/><br>';
?>
<input type="button" name="button_submit" value="Valider"onclick="updatePointsCreationPerso()" />
</fieldset>
</form>

<form action="index.php?view=detailsperso&format=raw&option=com_perso_norlande&task=updateCristauxPerso" method="post">
<fieldset>  
  <legend align="left">Cristaux</legend>
<?php 

//<label for="cristaux_incolores">Cristaux Incolores</label>
// <input type="text" id="cristaux_incolores" name="cristaux_incolores" size="3" maxlength="3" value="0"/><br>
foreach(ClasseXP::getTypesCristaux() as $famille) {
	$val = $xp->getCristaux($famille);
	echo '<label for="cristaux_'.$famille.'">Cristaux '.$famille.'</label>';
	echo '<input type="text" id="cristaux_'.$famille.'" name="cristaux_'.$famille.'" class="shortNb" value="'.$val.'"/><br>';
}
?>
<input type="submit" name="button_submit" value="Valider" />
</fieldset>
</form>


<form>
<fieldset>
  <legend align="left">Entrainement</legend>

<h5>Entrainements acquis :</h5>
<?php
$entrainements = $xp->getEntrainements();
echo "<table id=tbl_entrainements>";
if(count($entrainements) == 0) {
	echo '<tr id="row_empty"><td>Aucun entrainement</td>';
}
else {
	foreach($entrainements as $id_competence => $nom_competence) {
		echo '<tr id="row_entrainement_'.$id_competence.'"><td>'.$nom_competence.'</td>';
		echo '<td><input type="button" id="entrainement_'.$id_competence.'" name="button_submit" value="Supprimer" onclick="deleteEntrainement('.$id_competence.')"/></td></tr>';
	}
}
echo "</table>";
?>

<h5>Ajouter un entrainement :</h5>
<label for="recherche_entrainement">Recherche : </label>
<input type="text" name="recherche_entrainement" id="recherche_entrainement"/><br>
</fieldset>
</form>
<br>

<script type="text/javascript" >

$(function() {
	$('#recherche_entrainement').autocomplete({
		source : 'index.php?option=com_perso_norlande&task=searchEntrainement',
		focus: function( event, ui ) {
                  $( "#recherche_entrainement" ).val( ui.item.label );
                     return false;
               },
		select: function(event, ui) {
			add_entrainement(ui.item.value);
			return false;
		},
	});
});


</script>

<form>
<fieldset>
  <legend align="left">Background</legend>
<textarea id="histoire" name="histoire" rows="12"></textarea>
<br>

<input type="submit" name="button_submit" value="Valider" />
</fieldset>
</form>

<?php } ?>

	<div id="alert" style="display:none; cursor: default"> 
     <p id="alert_msg">Would you like to contine?.</p>
     <input type="button" id="alert_ok" value="OK" />
	</div> 
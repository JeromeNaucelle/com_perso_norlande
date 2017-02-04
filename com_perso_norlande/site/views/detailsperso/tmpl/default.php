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

<?php if($this->perso == null
			|| $this->edit_orga) {
?>
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

<?php 
	}
?>


<?php if($this->edit_orga) { ?>
<form>
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

<form>
<fieldset>  
  <legend align="left">Associer un utilisateur au personnage</legend>
  <?php 
  if($this->owner == null) {
  	echo "<p>Ce personnage n'est lié à aucun utilisateur.</p>";
  } else {
  	echo "<p>Attention ! Ce personnage est actuellement lié à : {$this->owner->name} ({$this->owner->username})</p>";
  }
  ?>
<label for="recherche_user">Nom d'utilisateur : </label><input id="recherche_user" type="text" name="recherche_user" /><br>
<input type="submit" name="button_submit" value="Sélectionner" />
</fieldset>
</form>



<script type="text/javascript" >

$(function() {
	$('#recherche_user').autocomplete({
		source : 'index.php?option=com_perso_norlande&task=searchUser',
		focus: function( event, ui ) {
                  $( "#recherche_user" ).val( ui.item.label );
                     return false;
               },
		select: function(event, ui) {
			document.location.href="index.php?option=com_perso_norlande&task=associatePersoUser&user_id="+ui.item.value;
			return false;
		},
	});
});

</script>

<?php } ?>









<?php if($this->perso !=NULL) { 
if( $this->edit_orga ){
	$display = '';
	$disabled = '';
} else {
	$display = 'style="display:none;"';
	$disabled = 'disabled';
}

?>
<br>
<h3>Personnage : <?php echo htmlspecialchars($this->perso->getNom().' ('.$this->perso->getLignee().')'); ?></h3>

<form id="formPointsCreation">
<fieldset>  
  <legend align="left">Points de Cr&eacute;ation</legend>
<?php 
$xp = $this->perso->getXp();

echo '<label for="pointsCreation">Points de cr&eacute;ation</label>';
echo '<input type="text" id="pointsCreation" name="pointsCreation" class="shortNb" value="'.$xp->getPointsCreation().'" '.$disabled.'/><br>';
?>
<input type="button" name="button_submit" value="Valider" onclick="updatePointsCreationPerso()" <?php echo $display;?> />
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
	echo '<input type="text" id="cristaux_'.$famille.'" name="cristaux_'.$famille.'" class="shortNb" value="'.$val.'" '.$disabled.'/><br>';
}
?>
<input type="submit" name="button_submit" value="Valider" <?php echo $display;?>/>
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
		if($this->edit_orga) {
			echo '<td><input type="button" id="entrainement_'.$id_competence.'" name="button_submit" value="Supprimer" onclick="deleteEntrainement('.$id_competence.')"/></td></tr>';
		}
	}
}
echo "</table><br>";

if( $this->edit_orga ) {
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

<?php } ?>

<div class="first-fieldset">
    <form name="formname" id="formMonnaie">
        <fieldset class="adminform">
            <legend>Monnaie</legend>
            <?php foreach ($this->form->getFieldset('monnaie') as $field): ?>
                <?php echo $field->label; ?>
                
                <?php if( !$this->edit_orga) {
                	$field->disabled = true;
                	} ?>
                	
                <?php echo $field->input.'<br>'; ?>
            <?php endforeach; ?>
            <input type="button" name="button_submit" value="Enregistrer" onclick="updateMonnaie()" <?php echo $display;?>/>
        </fieldset>
    </form>
</div>

<?php echo "Actions de guerre : ". $this->synthese->getActionsGuerre(); ?>
<?php echo "Rumeurs : ". $this->synthese->getRumeurs(); ?>

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
	
	<div id="question" style="display:none; cursor: default"> 
        <p id="question_msg"></p>
        <div id="question_options"></div>
        <input type="button" id="question_ok" value="Supprimer" />
        <input type="button" id="question_cancel" value="Annuler" />
	</div> 
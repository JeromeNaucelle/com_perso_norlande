<br>
<h3 id="nomPerso">Personnage : <?php echo htmlentities($this->perso->getNom()); ?></h3>
<?php
if( !$this->perso->userHasValidate() ) {
	$validation_button_display = 'style="display: inline-block;"';
} else {
	$validation_button_display = 'style="display:none;"';
}?>
<input  type="button" value="Valider le personnage" onclick="javascript:validationPersoUser();" <?php echo $validation_button_display; ?>/>

<h3 id="lignee"><?php echo '('.$this->perso->getLignee().')'; ?></h3>

<?php if( $this->edit_orga ){ 

	include 'form_anciennete_perso.php';
	include 'form_association_user_perso.php';

} 
?>

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
<input type="submit" name="button_submit" value="Valider" <?= $display ?>/>
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

	include 'form_add_entrainement.php';
} 
?>

<div class="first-fieldset">
    <form name="formname" id="formMonnaie">
        <fieldset class="adminform">
            <legend>Monnaie</legend>
            <?php foreach ($this->form->getFieldset('monnaie') as $field): ?>
                <?php echo $field->label; ?>
                
                <?php $field->disabled = !$this->edit_orga; ?>
                	
                <?= $field->input.'<br>' ?>
            <?php endforeach; ?>
            <input type="button" name="button_submit" value="Enregistrer" onclick="updateMonnaie()" <?= $display ?>/>
        </fieldset>
    </form>
</div>

<form id="formArmure">
<fieldset>
  <legend align="left">Armure</legend>
  <label for="armure">Choix de l'armure :</label>
<select id="armure" name="armure" onchange="updateArmure()" <?= $user_validation_disabled ?> >
<?php foreach ($this->enumArmure as $field): ?> 
	<?php $armureSelected = ($this->perso->getArmure() == $field ? ' selected': ''); ?> 
	<?php echo "<option value=\"$field\"$armureSelected> $field</option>"; ?>
<?php endforeach; ?>
</select>

</fieldset>
</form>


<form action="index.php?view=detailsperso&option=com_perso_norlande&task=updateHistoire" method="post">
<fieldset>
  <legend align="left">Background</legend>
<p><b>Histoire :</b></p>
<textarea class="long_text" id="histoire" name="histoire" <?= $user_validation_disabled ?>>
<?php echo htmlentities($this->perso->getHistoire()); ?>
</textarea>
<br>

<input type="submit" name="button_submit" value="Valider" <?= $user_validation_disabled ?>/>
</fieldset>
</form>

<form action="index.php?view=detailsperso&option=com_perso_norlande&task=updateReliquat" method="post">
<fieldset>
  <legend align="left">Reliquat des années précédentes</legend>

<textarea class="long_text" id="reliquat" name="reliquat" <?= $disabled ?>>
<?php echo htmlentities($this->perso->getReliquat()); ?>
</textarea>
<br>

<input type="submit" name="button_submit" value="Valider"  <?= $display ?>/>
</fieldset>
</form>



<fieldset>
  <legend align="left">Visualisation</legend>
  
	<div class="center_wrapper">
		<input type="button" class="buttonFiche" value="Voir la fiche" onclick="javascript:open_infos();"/>
		<input type="button" class="buttonFiche" value="Télécharger la fiche" onclick="javascript:download_fiche();"/>
	</div>

</fieldset>

<fieldset>
  <legend align="left">Suppression</legend>
  
	<div class="center_wrapper">
		<input type="button" class="buttonFiche" value="Supprimer le personnage" onclick="javascript:delete_perso();"/>
	</div>

</fieldset>




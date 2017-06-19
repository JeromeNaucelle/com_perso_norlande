<form id="formAnciennete">
<fieldset>  
  <legend align="left">Ancienneté</legend>

<label for="anciennete">Ancienneté : </label>
<?php echo '<input id="anciennete" type="text" name="anciennete" class="shortNb" value="'.$this->perso->getAnciennete().'"/> ans'; ?>
<br>
<input type="button" name="button_submit" onclick="updateAnciennete()" value="Valider" />
</fieldset>
</form>
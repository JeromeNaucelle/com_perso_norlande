
<fieldset>  
  <legend align="left">Cr&eacute;ation d&apos;un personnage</legend>
<form action="index.php?view=detailsperso&format=raw&option=com_perso_norlande&task=createPerso" method="post">
	<label for="nom_perso">Nom : </label><input id="nom_perso" type="text" name="nom_perso" /><br>
	<label for="lignee_perso">Lignee : </label><select id="lignee_perso" name="lignee_perso">
	<?php
	foreach(Lignees::$lignees as $key=>$lignee) {
		echo '<option value="'.$key.'">'.$lignee.'</option>';
	}
	?>
	</select><br>
	<input type="submit" name="button_submit" value="Créer" />
</form>
</fieldset>


<fieldset>  
  <legend align="left">Cr&eacute;ation d&apos;un personnage</legend>
  
  <h6>Incarner un personnage déjà joué</h6>
  <p>Si vous souhaitez incarner un personnage que vous avez déjà jouer, veuillez remplir le formulaire ci-dessous. 
  Il vous permettra de prendre contact avec l'orga de la lignée qu vous souhaitez rejoindre cette année. Vous recevrez ensuite
  un mail pour vous avertir lorsque votre personnage sera prêt à recevoir vos dernières informations.</p>
  
<form id="form_ask_perso_association">
	<label for="lignee_partie_precedente">Cette année je fais partie de la lignée :  </label><br>
	<select id="lignee_partie_precedente" name="lignee_partie_precedente">
	<?php
	foreach(Lignees::$lignees as $key=>$lignee) {
		echo '<option value="'.$key.'">'.$lignee.'</option>';
	}
	?>
	</select><br>  
		<textarea class="long_text" name="demande_assoc_user_perso">
Mon cher orga,
Merci de m'accueillir cette année. J'incarnais NOM_PERSONNAGE de la lignée LIGNEE_PRECEDENTE.
J'attends des nouvelles de mon personnage avec impatience :)
	</textarea>
	<input type="button" name="button_submit" value="Envoyer" onclick="javascript:askForPersoAssociation()"/>
</form>

<br>  
	<h6>Incarner un nouveau personnage</h6>
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

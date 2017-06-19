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
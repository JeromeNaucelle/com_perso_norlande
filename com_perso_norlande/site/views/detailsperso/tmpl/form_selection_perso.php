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
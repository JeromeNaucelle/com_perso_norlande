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
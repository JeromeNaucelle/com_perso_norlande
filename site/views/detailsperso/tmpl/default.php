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

$js = <<<JS

	function add_entrainement(competence_id){
		var url = "index.php?option=com_perso_norlande&task=addEntrainement&competence_id="+competence_id;
		$.ajax(
	 	{
	     // Post select to url.
	     type : 'get',
	     url : url,
	     dataType : 'json', // expected returned data format.
	     success : function(data)
	     {
	     		$( "#row_empty").remove();
	     		$( "#tbl_entrainements" ).append( '<tr id="row_entrainement_'+competence_id+'"><td>'+data[competence_id]+'</td>' );
				$( "#row_entrainement_"+competence_id ).append( '<td><input type="button" id="entrainement_'+competence_id+'" name="button_submit" value="Supprimer" onclick="deleteEntrainement('+competence_id+')"/></td>' );
	     },
	     complete : function(data)
	     {
	         // do something, not critical.
	     }
		});
	}

	function deleteEntrainement(competence_id){
		var nom_entrainement = $( "#row_entrainement_"+competence_id).text();
		$("#del_entrainement_id").val(competence_id);
		$("#question_msg").text("Voulez-vous supprimer l'entrainement du "+nom_entrainement +" ?");
		$.blockUI({ message: $('#question'), css: { width: '275px' } }); 
		
	}
	
	
    $(document).ready(function() {
 
     $('#question_cancel').click(function() { 
         $.unblockUI(); 
         return false; 
     }); 
     
     $('#question_ok').click(function() { 
     		var competence_id = $("#del_entrainement_id").val();
			var url = "index.php?option=com_perso_norlande&task=deleteEntrainement&competence_id="+competence_id;
			$.ajax(
	    	{
	        // Post select to url.
	        type : 'get',
	        url : url,
	        dataType : 'json', // expected returned data format.
	        success : function(data)
	        {
	        		$( "#row_entrainement_"+competence_id).remove();
	        		var rows = $( "#tbl_entrainements" ).find("tr");
	        		if(rows.length == 0) {
	        			$( "#tbl_entrainements" ).append( '<tr id="row_empty"><td>Aucun entrainement</td>' );
	        		}
	        },
	        complete : function(data)
	        {
	            $.unblockUI(); 
	        }
	    });
     }); 
 
 	}); 
JS;

// Add Javascript
$doc->addStyleSheet("components/com_perso_norlande/media/perso_norlande/css/style.css",'text/css',"screen");
$doc->addStyleSheet("components/com_perso_norlande/media/perso_norlande/css/jquery-ui.min.css",'text/css',"screen");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery-3.1.1.min.js");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery-ui-1.12.1.min.js");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery.blockUI.js");
$doc->addScriptDeclaration($js);

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
<input type="submit" name="button_submit" value="Créer" />
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

<form action="index.php?view=detailsperso&format=raw&option=com_perso_norlande&task=updateCristauxPerso" method="post">
<fieldset>  
  <legend align="left">Cristaux</legend>
<?php 
$xp = $this->perso->getXp();

//<label for="cristaux_incolores">Cristaux Incolores</label>
// <input type="text" id="cristaux_incolores" name="cristaux_incolores" size="3" maxlength="3" value="0"/><br>
foreach(ClasseXP::get_types_cristaux() as $famille) {
	$val = $xp->get_cristaux($famille);
	echo '<label for="cristaux_'.$famille.'">Cristaux '.$famille.'</label>';
	echo '<input type="text" id="cristaux_'.$famille.'" name="cristaux_'.$famille.'" size="3" maxlength="3" value="'.$val.'"/><br>';
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
$entrainements = $xp->get_entrainements();
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

	<div id="question" style="display:none; cursor: default"> 
        <p id="question_msg">Would you like to contine?.</p>
        <input type="hidden" id="del_entrainement_id"/>
        <input type="button" id="question_ok" value="Oui" />
        <input type="button" id="question_cancel" value="Annuler" />
	</div> 
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
	     		alert("ajout OK");
	     		alert('<label id="label_entrainement_'+competence_id+'" for="entrainement_'+competence_id+'">'+data['competence_id']+'</label>');
	     		$( "#entrainements_acquis" ).append( '<label id="label_entrainement_'+competence_id+'" for="entrainement_'+competence_id+'">'+data[competence_id]+'</label>' );
				$( "#entrainements_acquis" ).append( '<input type="button" id="entrainement_'+competence_id+'" name="button_submit" value="Supprimer" onclick="deleteEntrainement('+competence_id+')"/>' );
				$( "#entrainements_acquis" ).append('<br>');
	     },
	     complete : function(data)
	     {
	         // do something, not critical.
	     }
		});
	}

	function deleteEntrainement(competence_id){
		var url = "index.php?option=com_perso_norlande&task=deleteEntrainement&competence_id="+competence_id;
		$.ajax(
    	{
        // Post select to url.
        type : 'get',
        url : url,
        dataType : 'json', // expected returned data format.
        success : function(data)
        {
        		alert("suppression de la competence "+competence_id);
        		$( "#label_entrainement_"+competence_id).remove();
        		$( "#entrainement_"+competence_id).remove();
        },
        complete : function(data)
        {
            // do something, not critical.
        }
    });
	}
JS;

// Add Javascript
$doc->addStyleSheet("components/com_perso_norlande/media/perso_norlande/css/style.css",'text/css',"screen");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery-3.1.1.min.js");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery-ui-1.12.1.min.js");
$doc->addScriptDeclaration($js);

?>

  
<?php include(JPATH_COMPONENT . '/includes/menu.php'); ?>

<h1>Informations</h1>

<form action="index.php?view=detailsperso&format=raw&option=com_perso_norlande&task=updateDetailsPerso" method="post">


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
<label for="recherche_entrainement">Ajouter un entrainement</label>
<input type="text" name="recherche_entrainement" id="recherche_entrainement"/><br>

<div id="entrainements_acquis">
<h3>Entrainements acquis :</h3>
<?php
error_log('affichage des entrainements 1');
$entrainements = $xp->get_entrainements();
//error_log('affichage des entrainements'.var_dump($entrainements));
foreach($entrainements as $id_competence => $nom_competence) {
	echo '<label id="label_entrainement_'.$id_competence.'" for="entrainement_'.$id_competence.'">'.$nom_competence.'</label>';
	echo '<input type="button" id="entrainement_'.$id_competence.'" name="button_submit" value="Supprimer" onclick="deleteEntrainement('.$id_competence.')"/><br>';
}
?>
</div>
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

<label for="histoire">Histoire :</label>
<textarea id="histoire" name="histoire" rows="12"></textarea>
<br>

<input type="submit" name="button_submit" value="Valider" />
</form>
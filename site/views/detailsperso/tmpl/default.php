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
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery-3.1.1.min.js");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery-ui-1.12.1.min.js");
//$doc->addScriptDeclaration("");

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
<input type="text" name="recherche_entrainement" id="recherche_entrainement"/>

<script type="text/javascript" >
$(function() {
	$('#recherche_entrainement').autocomplete({
		source : 'index.php?option=com_perso_norlande&task=searchEntrainement'
	});
});

</script>

<label for="histoire">Histoire :</label>
<textarea id="histoire" name="histoire" rows="12"></textarea>
<br>

<input type="submit" name="button_submit" value="Valider" />
</form>
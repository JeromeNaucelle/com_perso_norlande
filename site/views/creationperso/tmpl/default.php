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
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery.blockUI.js");
$doc->addScript("https://www.gstatic.com/charts/loader.js");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/creationperso.js");

echo "<input type='hidden' id='ajax_url' value='index.php?format=raw&option=com_perso_norlande&task=getArbreMaitrise&competence=".$this->competence."'>"; ?>


<h1>Test</h1>

  
<?php include(JPATH_COMPONENT . '/includes/menu.php'); ?>

<?php if($this->perso == NULL) {
	echo "Vous devez d&apos;abord cr&eacute;er ou s&eacute;lectionner un Personnage.";
	}
	else {
?>
	  		<ul id="submenu">
<?php
$menu_list = $this->list_maitrise;
for($i=0; $i < count($menu_list); $i++) 
{
	echo '<li><a href="index.php?option=com_perso_norlande&famille='.$this->famille.'&competence='.$menu_list[$i]['competence_id'].'">'.htmlentities($menu_list[$i]['competence_nom']).'</a>';
	echo "</li>";
}
?>	  		
		</ul>
	</div>
	<div id="affichage_perso"></div>
    <p id="chart_p"></p>
	</div>
	
	<div id="alert" style="display:none; cursor: default"> 
        <p id="alert_msg">Would you like to contine?.</p>
        <input type="button" id="alert_ok" value="OK" />
	</div> 
	
	<div id="question" style="display:none; cursor: default"> 
        <p id="question_msg">Would you like to contine?.</p>
        <div id="question_options"></div>
        <input type="button" id="question_ok" value="Valider" />
        <input type="button" id="question_cancel" value="Annuler" />
	</div> 
	
	<div id="question_dep_xp" style="display:none; cursor: default;max-width:300px;">
	<input type="hidden" name="niveauCompetence" id="niveauCompetence"></input>
	<p style="text-align:center">Pour acqu&eacute;rir cette comp&eacute;tence vous devez :</p>
	<form id="depense_points_creation" style="display:none">
		<fieldset>
		<legend>Points de cr&eacute;ation</legend>
		<input type="hidden" name="typeXp" value="points_creation"></input>

		<input type="button" value="Valider" id="submit_points_creation" onclick="checkNbPointsCreation()"/>
		</fieldset>
	</form>	
	
	<form id="depense_cristaux" style="display:none">
		<fieldset>
		<legend>Cristaux</legend>
		<input type="hidden" name="typeXp" value="cristaux"></input>
		
		<input type="button" value="Valider" id="submit_cristaux" onclick="checkNbCristaux()"/>
		</fieldset>
	</form>
	
	
	<form method="post" id="depense_entrainement" style="display:none">
		<fieldset>
		<legend>Entrainements</legend>
		<input type="hidden" name="typeXp" value="entrainement"></input>
		<p>Utiliser un entrainement :</p>
		<input type="button" value="Valider" id="submit_entrainement" onclick="postChoixDepenseXP('depense_entrainement')">
		</fieldset>
	</form>
	<input type="button" value="Annuler" style="width:100%" onclick="cancelDepenseCristaux()"/>
	</div>	

<?php 
}
?>

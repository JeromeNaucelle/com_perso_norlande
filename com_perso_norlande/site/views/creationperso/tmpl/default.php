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
$doc->addStyleSheet("components/com_perso_norlande/media/perso_norlande/css/style{$this->famille}.css",'text/css',"screen");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery-3.1.1.min.js");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery.blockUI.js");
$doc->addScript("https://www.gstatic.com/charts/loader.js");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/creationperso.js");

echo "<input type='hidden' id='ajax_url' value='index.php?format=raw&option=com_perso_norlande&task=getArbreMaitrise&competence=$this->competence'>"; ?>


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
	echo '<li><a href="index.php?option=com_perso_norlande&view=creationperso&famille='.$this->famille.'&competence='.$menu_list[$i]['competence_id'].'#content">'.htmlentities($menu_list[$i]['competence_nom']).'</a>';
	echo "</li>";
}
?>	  		
		</ul>
	</div>
    <p id="chart_p"></p>
	</div>
	
	<div id="alert" style="display:none; cursor: default"> 
        <p id="alert_msg">Would you like to contine?.</p>
        <input type="button" id="alert_ok" value="OK" />
	</div> 
	
	<div id="question" style="display:none; cursor: default"> 
        <p id="question_msg">Souhaitez-vous oublier cette comp&eacute;tence ?</p>
        <div id="question_options"></div>
        <input type="button" id="question_ok" value="Oublier" />
        <input type="button" id="question_cancel" value="Annuler" />
	</div> 
	
	<div id="question_dep_xp" style="display:none; cursor: default;max-width:300px;">
	</div>	

<?php 
}
?>

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
$doc->addStyleSheet("components/com_perso_norlande/media/perso_norlande/css/jquery-ui.min.css",'text/css',"screen");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery-3.1.1.min.js");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery-ui-1.12.1.min.js");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery.blockUI.js");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/detailsperso.js");

include(JPATH_COMPONENT . '/includes/menu.php'); 
require_once(JPATH_COMPONENT . '/includes/define.php'); 
?>

<div id="alert" style="display:none; cursor: default"> 
  <p id="alert_msg">Would you like to contine?.</p>
  <input type="button" id="alert_ok" value="OK" />
</div> 

<div id="question" style="display:none; cursor: default"> 
     <p id="question_msg"></p>
     <div id="question_options"></div>
     <input type="button" id="question_ok" value="Supprimer" />
     <input type="button" id="question_cancel" value="Annuler" />
</div> 


<?php
if($this->perso == null
	&& !$this->edit_orga) {
		
	include 'form_creation_perso.php';
}

if($this->edit_orga) { 
	include 'orga_creation_perso.php';
	include 'form_selection_perso.php';

} 


if( $this->edit_orga ){
	$display = '';
	$disabled = '';
} else {
	$display = 'style="display:none;"';
	$disabled = 'disabled';
}


if($this->perso !=NULL) { 

	if( $this->perso->userHasValidate() 
			&& !$this->edit_orga){
		$user_validation_disabled = 'disabled';
		$user_validation_display = 'style="display:none;"';
	} else {
		$user_validation_disabled = '';
		$user_validation_display = '';
	}

	include 'desc_perso.php';
}

?>
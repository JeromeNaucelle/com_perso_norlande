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

if($this->perso == null
	|| $this->edit_orga) {
		
	include 'form_creation_perso.php';
}

if($this->edit_orga) { 
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
	include 'desc_perso.php';
}

?>
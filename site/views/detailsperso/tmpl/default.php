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
?>


<h1>Test</h1>

  
  <div id="content">
  		<ul id="menu-cat-maitrises">
   		<li><a href="index.php?famille=Occultisme">Occultisme</a>
   		</li>
    		<li><a href="index.php?famille=Belligerance">Belligerance</a>
   		</li>
   		<li><a href="index.php?famille=Societe">Societe</a>
   		</li>
   		<li><a href="index.php?famille=Intrigue">Intrigue</a>
   		</li>
		</ul>
	<div>
	  	Bla
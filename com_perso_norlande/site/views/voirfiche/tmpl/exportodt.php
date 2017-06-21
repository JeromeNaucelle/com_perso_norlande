<?php

use Odtphp\Odf;

include('components/com_perso_norlande/helpers/template.php');
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

//$doc->addStyleSheet("components/com_perso_norlande/media/perso_norlande/css/fiche_perso.css",'text/css',"screen");

require_once 'odtphp/vendor/autoload.php';

$odf = new Odf("components/com_perso_norlande/views/voirfiche/tmpl/odtphp/tests/tutoriel1.odt");

$odf->setVars('titre', 'PHP: Hypertext PreprocessorPHP: Hypertext Preprocessor');

$message = "PHP (sigle de PHP: Hypertext Preprocessor), est un langage de scripts libre 
principalement utilisé pour produire des pages Web dynamiques via un serveur HTTP, mais 
pouvant également fonctionner comme n'importe quel langage interprété de façon locale, 
en exécutant les programmes en ligne de commande.";

$odf->setVars('message', $message);

// We export the file
$odf->exportAsAttachedFile();

JFactory::getApplication()->close();

?>

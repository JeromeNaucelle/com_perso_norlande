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
 
/**
 * HTML View class for the HelloWorld Component
 *
 * @since  0.0.1
 * MyComponentViewMyPage
 */
class Perso_NorlandeViewDetailsPerso extends JViewLegacy
{
	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{		
		// Assign data to the view
		//$this->setModel( $this->getModel( 'perso_norlande' ), true );
		$model = null;
		$model = $this->getModel('detailsperso');
		
		if($model != null) {
			$this->setModel($model);
		} else {
			JLog::add(JText::_('Model creationperso non trouvé'), JLog::WARNING, 'jerror');		
			error_log ('modele non trouve');
		}
		
		$session = JFactory::getSession();
		$this->perso = NULL;
		$perso_id = $session->get('perso_id',NULL);
		if($perso_id == NULL)
		{
			JLog::add(JText::_('Pas de personnage actif'), JLog::WARNING, 'jerror');
		} else {
			$perso = PersoHelper::getPersoById($perso_id);
			if($perso == null) {
				JLog::add(JText::_('Perso non trouvé'), JLog::WARNING, 'jerror');
			} else {	
				$this->perso = $perso;
				$session->set( 'perso', serialize($perso));
			}
		}		

		// Display the view
		parent::display($tpl);
	}
}

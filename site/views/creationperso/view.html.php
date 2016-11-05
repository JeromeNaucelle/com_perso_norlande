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
class Perso_NorlandeViewCreationPerso extends JViewLegacy
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
		
		$jinput = JFactory::getApplication()->input;
		$this->famille = $jinput->get('famille', 'Belligerance', 'STR');
		// Assign data to the view
		//$this->setModel( $this->getModel( 'perso_norlande' ), true );
		$model = null;
		$model = $this->getModel('creationperso');
		
		if($model != null) {
			$this->setModel($model);
		} else {
			JLog::add(JText::_('Model creationperso non trouvé'), JLog::WARNING, 'jerror');		
		}
		
		/*$perso = $model->getPerso('firstPerso');
		if($perso == null) {
			JLog::add(JText::_('Perso non trouvé'), JLog::WARNING, 'jerror');		
		}*/
		
		$this->list_maitrise = $model->getMaitrisesFromFamille();
		$this->competence = $jinput->get('competence', '1', 'INT');
		
		
		// Display the view
		parent::display($tpl);
	}
}

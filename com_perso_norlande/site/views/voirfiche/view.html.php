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

require_once JPATH_COMPONENT . '/includes/Perso.php';
require_once JPATH_COMPONENT . '/includes/Competence.php';
require_once JPATH_COMPONENT . '/includes/SyntheseCompetences.php';
 
/**
 * HTML View class for the HelloWorld Component
 *
 * @since  0.0.1
 * MyComponentViewMyPage
 */
class Perso_NorlandeViewVoirFiche extends JViewLegacy
{
	
	function listCompetences($model, $famille_comp) {
		
	}
	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{		
		$this->perso = NULL;
		$session = JFactory::getSession();
		$perso_id = $session->get('perso_id',NULL);
		
		if($perso_id == NULL)
		{
			JLog::add(JText::_('Aucun personnage sélectionné'), JLog::WARNING, 'jerror');	
		}
		else {
			$this->perso = PersoHelper::getPersoById($perso_id);
			if($this->perso == null) {
				JLog::add(JText::_('Perso non trouvé'), JLog::WARNING, 'jerror');		
			}
		}
		
		$this->competencesClassees = array();
		foreach(Familles::$familles as $famille) {
			$classement = new CompetenceFamille();
			$classement->setFamille($famille);
			
			foreach($this->perso->getCompetences() as $comp) {
				if(strcasecmp($comp->getFamille(), $famille) == 0) {
					$classement->addCompetence($comp);
				}
			}
			$this->competencesClassees[$famille] = $classement;
		}
		
		$this->synthese = SyntheseCompetences::create($this->perso->getId(), 
			$this->perso->getLignee(),
			$this->competencesClassees);
		

		// Display the view
		parent::display($tpl);
	}
}

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
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
jimport('joomla.log.log');

//require(JPATH_ROOT."/components/com_perso_norlande/includes/Perso.inc");
require_once JPATH_COMPONENT . '/includes/Perso.php';
 
/**
 * HelloWorld Model
 *
 * @since  0.0.1
 */
class Perso_NorlandeModelCreationPerso extends JModelItem
{
	/**
	 * @var string message
	 */	
	
	public function getPerso($nom) 
	{
		JLog::add(JText::_('test 1'), JLog::WARNING, 'jerror');
		$db = JFactory::getDbo();
 
		// Create a new query object.
		$query = $db->getQuery(true);
 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('id', 'nom', 'lignee', 'maitrises')));
		$query->from($db->quoteName('persos'));
		$query->where($db->quoteName('nom') . ' = '. $db->quote($nom));
		
		
		 //SELECT * FROM `persos` p INNER JOIN `perso_maitrises` mp ON mp.id_perso = p.id
		//WHERE nom='firstPerso'
		
		
		
		
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadAssocList();
		print_r($results);
		$perso = Perso::create($results);
		
		JLog::add(JText::_($perso->getNom()), JLog::WARNING, 'jerror');
		return $perso;
	}
 

	public function getArbreMaitrise($competence_id)
	{
		$return = array();
		
		$db = JFactory::getDbo();
 
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('a.competence_id','a.competence_nom', 'a.parent_id')));
		$query->from($db->quoteName('competences', 'a'));
		$query->join('INNER', $db->quoteName('competences', 'b') . ' ON (' . $db->quoteName('a.maitrise') . ' = ' . $db->quoteName('b.competence_nom') . ')');
		$query->where($db->quoteName('b.competence_id') . ' = ' . $competence_id);
		JLog::add(JText::_($query), JLog::WARNING, 'jerror');
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$results = $db->loadAssocList();
		
		
		for($i=0; $i<count($results); $i++)
		{
			$parent = '';
			$maitrise = $results[$i];
			if($maitrise['parent_id'] != 0){
				$parent = $maitrise['parent_id'];
			}
			$return[] = array(array('v' => $maitrise['competence_id'],'f'=>'<h1>'.htmlentities($maitrise['competence_nom']).'</h1>'), $parent);
		}
		
		return $return;
	}
	
	public function getMaitrisesFromFamille()
	{
		$jinput = JFactory::getApplication()->input;
		$famille = $jinput->get('famille', 'Belligerance', 'STR');
		$db = JFactory::getDbo();
 
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('competence_id', 'competence_nom')));
		$query->from($db->quoteName('competences'));
		$query->where($db->quoteName('parent_id') . " = 0", 'AND' );
		$query->where('famille = '. $db->quote($famille));
		JLog::add(JText::_($query), JLog::WARNING, 'jerror');
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		return $db->loadAssocList();
	}
	
	public function getCompetencesPerso($perso_id)
	{
		$db = JFactory::getDbo();
 
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('competences'));
		$query->where($db->quoteName('parent_id') . " = 0", 'OR' );
		$query->where('famille = '. $db->quote($famille));
		JLog::add(JText::_($query), JLog::WARNING, 'jerror');
	}
	
	public function getMaitrise($nom_maitrise)
	{
		$db = JFactory::getDbo();
 
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select("*");
		$query->from($db->quoteName('maitrises'));
		$query->where($db->quoteName('nom_format') . ' = '. $db->quote($nom_maitrise));
		$db->setQuery($query);
		$results = $db->loadObjectList();
	}
}

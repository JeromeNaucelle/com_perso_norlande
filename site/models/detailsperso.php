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
require_once JPATH_COMPONENT . '/includes/Arbre.php';
 
/**
 * HelloWorld Model
 *
 * @since  0.0.1
 */
class Perso_NorlandeModelDetailsPerso extends JModelItem
{

	public function setCristaux($tab_cristaux, $perso) {
		$db = JFactory::getDbo();
 
		// Create a new query object.
		$query = $db->getQuery(true);
		
		$fields = array();
		foreach($tab_cristaux as $type => $val) {
			array_push($fields, $db->quoteName($type) . ' = ' . $val);
		}
		
		$conditions = $db->quoteName('id') . ' =  ' . $perso->getId();
		$query->update($db->quoteName('persos'))->set($fields)->where($conditions);
		
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		//TODO : mettre à jour le perso dans la session
		$result = $db->execute();
	}
	
	public function addEntrainement($perso, $competence_id) {
		$db = JFactory::getDbo();
 
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select('competence_id, competence_nom')
		->from($db->quoteName('competences'))
		->where($db->quoteName('competence_id').' = '.$competence_id);
		
		$db->setQuery($query);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$tmp = $db->loadAssoc();
		$perso->addEntrainement($tmp['competence_id'], $tmp['competence_nom']);
		$result = $perso->getXp()->get_entrainements();
		
		$query = $db->getQuery(true);
		$field = $db->quoteName('entrainements') . ' = ' . $db->quote(json_encode($result));
		
		$conditions = $db->quoteName('id') . ' =  ' . $perso->getId();
		$query->update($db->quoteName('persos'))->set($field)->where($conditions);
		$db->setQuery($query);
		$db->execute();
		
		return $result;
	}
	
	public function deleteEntrainement($perso, $competence_id) {
		$db = JFactory::getDbo();
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data)
		$result = $perso->getXp()->get_entrainements();
		unset($result[$competence_id]);
		
		$query = $db->getQuery(true);
		$field = $db->quoteName('entrainements') . ' = ' . $db->quote(json_encode($result));
		
		$conditions = $db->quoteName('id') . ' =  ' . $perso->getId();
		$query->update($db->quoteName('persos'))->set($field)->where($conditions);
		error_log($query->__toString);	
		$db->setQuery($query);
		$result = $db->execute();
			
		
		return json_encode($result);
	}
}

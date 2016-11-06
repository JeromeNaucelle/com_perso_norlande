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
		$query->select($db->quoteName(array('id', 'nom', 'lignee')));
		$query->from($db->quoteName('persos'));
		$query->where($db->quoteName('nom') . ' = '. $db->quote($nom));
		
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadAssoc();
		//print_r("function getPerso() : ".var_dump($results));
		
		$query_competences = $db->getQuery(true);
		$query_competences
			->select('a.*')
			->from($db->quoteName('competences', 'a'))
			->join('INNER', $db->quoteName('persos_competences', 'b') . ' ON (' . $db->quoteName('a.competence_id') . ' = ' . $db->quoteName('b.competence_id') . ')')
			->where($db->quoteName('b.id_perso') . ' = ' . $results['id']);
			
		$db->setQuery($query_competences);
		$result_competences = $db->loadAssocList();
			
		$perso = Perso::create($results, $result_competences);
		
		return $perso;
	}
	
	public function initEntraineurs()
	{
		$db = JFactory::getDbo();
		
		$query_max = "SELECT MAX(competence_id) FROM competences";
		$db->setQuery($query_max);
		$id_max = $db->loadResult();
 
		// Create a new query object.
		$query = $db->getQuery(true);
		//Select * From competences Where competence_id not in (Select parent_id from competences where 1)
		// on récupère toutes les compétences n'ayant pas "d'enfant"
		$query = "Select * From competences Where competence_id not in (Select parent_id from competences where 1)";
		$db->setQuery($query);
		$result_maitrises = $db->loadAssocList();
		
		foreach($result_maitrises as $maitrise)
		{
			error_log("ajout de l'entraineur ".$maitrise['competence_nom']);
			// Insert columns.
			$id_max += 1;
			$columns = array('competence_id', 'famille', 'maitrise', 'competence_nom', 'niveau','parent_id','entraineur');
 
			// Insert values.
			$values = array($id_max, $db->quote($maitrise['famille']), $db->quote($maitrise['maitrise']), $db->quote('Entraineur : '.$maitrise['competence_nom']), $maitrise['niveau']+1,$maitrise['competence_id'],1);
			$query_insert = $db->getQuery(true);
			$query_insert
    			->insert($db->quoteName('competences'))
    			->columns($db->quoteName($columns))
    			->values(implode(',', $values));
			
			$db->setQuery($query_insert);
			$db->execute();
		}
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
	
	
	public function getArbreMaitrisePhp($competence_id)
	{
		$return = array();
		
		$db = JFactory::getDbo();
 
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select($db->quoteName('a.*'));
		$query->from($db->quoteName('competences', 'a'));
		$query->join('INNER', $db->quoteName('competences', 'b') . ' ON (' . $db->quoteName('a.maitrise') . ' = ' . $db->quoteName('b.maitrise') . ')');
		$query->where($db->quoteName('b.competence_id') . ' = ' . $competence_id);
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$results = $db->loadAssocList();
		$arbre = new ArbreMaitrise($results);
		
		return $arbre;
	}
	
	public function getMaitrisesFromFamille($famille)
	{
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
	
	public function getDefaultMaitrise($famille) {
		$db = JFactory::getDbo();
 
		// Create a new query object.
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('competence_id'))
			->from($db->quoteName('competences'))
			->where($db->quoteName('parent_id') . " = 0", 'AND' )
			->where('famille = '. $db->quote($famille))
			->limit(1);
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		return $db->loadResult();
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

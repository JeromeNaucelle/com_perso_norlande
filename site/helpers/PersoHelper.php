<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/includes/Perso.php';


class PersoHelper {
	
	public static function insertPerso($nom, $lignee) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$columns = array('nom', 'lignee', 'entrainements');
		$entrainements = json_encode(array());
		$values = array($db->quote($nom), $db->quote($lignee), $db->quote($entrainements));
		 
		// Prepare the insert query.
		$query
		    ->insert($db->quoteName('persos'))
		    ->columns($db->quoteName($columns))
		    ->values(implode(',', $values));
		    
		$db->setQuery($query);
		$db->execute();
		return $db->insertid();
	}
	
	public static function persoExists($perso_id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$columns = array('id','nom','lignee');	
		$query->select($db->quoteName($columns));
		$query->from($db->quoteName('persos'));
		$query->where($db->quoteName('id') . ' = '. $perso_id);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$db->setQuery($query);
		$results = $db->loadAssoc();
 		
 		if(count($results) == 0) {
 			return false;
 		}
 		return $results['nom'];
	}
	
	public static function searchPersoByName($term) {
		$db = JFactory::getDbo();
 
		//recherche des résultats dans la base de données
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$columns = array('id','nom','lignee');		
		
		$query
		->select($db->quoteName($columns))
		->from($db->quoteName('persos'))
		->where($db->quoteName('nom').' LIKE '.$db->quote('%'.$term.'%'))
		->setLimit(10);
		
		error_log($query->__toString());
				
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
				 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		return $db->loadAssocList();
	}
	
	public static function getPersoById($id) {
		$db = JFactory::getDbo();
 
		// Create a new query object.
		$query = $db->getQuery(true);
 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select('*');
		$query->from($db->quoteName('persos'));
		$query->where($db->quoteName('id') . ' = '.$id);
		
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
	
}

?>
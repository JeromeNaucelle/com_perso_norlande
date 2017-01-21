<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/includes/Perso.php';


class PersoHelper {
	
	public static function insertPerso($nom, $lignee) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$columns = array('nom', 'lignee', 'points_creation', 'entrainements');
		$entrainements = json_encode(array());
		$values = array($db->quote($nom), $db->quote($lignee), 6, $db->quote($entrainements));
		 
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
	
	
	
	public static function useEntrainement($entrainementId, $competenceId, $perso) {
		$error = 0;
		$msg = "";
		$db = JFactory::getDbo();
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data)
		$result = $perso->getXp()->getEntrainements();
		unset($result[$entrainementId]);
		
		try
		{
			$db->transactionStart();
			
			// suppression de l'entrainement
			$query = $db->getQuery(true);
			$field = $db->quoteName('entrainements') . ' = ' . $db->quote(json_encode($result));
			
			$conditions = $db->quoteName('id') . ' =  ' . $perso->getId();
			$query->update($db->quoteName('persos'))->set($field)->where($conditions);
			$db->setQuery($query);
			$result = $db->execute();
			
			// ajout de la nouvelle compétence
			$query = $db->getQuery(true);
			$date = JFactory::getDate()->format('Y-m-d');
			$columns = array('id_perso', 'competence_id', 'date_acquisition', 'xp_used');
			$xp_used = json_encode(array("entrainement" => $entrainementId));
			$values = array($perso->getId(), $competenceId, $db->quote($date), $db->quote($xp_used));
			 
			// Prepare the insert query.
			$query
			    ->insert($db->quoteName('persos_competences'))
			    ->columns($db->quoteName($columns))
			    ->values(implode(',', $values));
			    
			$db->setQuery($query);
			$db->execute();
			$db->transactionCommit();
		}
		catch (Exception $e)
		{
		   // catch any database errors.
		   $db->transactionRollback();
		   $error = 1;
			$msg = "Erreur lors des changements en base de données lors de l'aprentissage de la compétence";
		}
		return array("error" => $error, "msg" => $msg);
	}
	
	public static function useCristaux($xpUsed, $competenceId, $perso) {
		$error = 0;
		$msg = "";
		$db = JFactory::getDbo();
		 
		$fields = array();
		$descXpUsed = array('cristaux'=>array());
		foreach(ClasseXP::getTypesCristaux() as $type) {
			if($xpUsed->getCristaux($type) > 0) {
				$newVal = $perso->getXp()->getCristaux($type) - $xpUsed->getCristaux($type);
				array_push($fields, $db->quoteName('cristaux_'.$type). ' = ' .$newVal);
				$descXpUsed['cristaux'][$type] = $xpUsed->getCristaux($type);
			}
		}
		$descXpUsed = json_encode($descXpUsed);
		
		try
		{
			$db->transactionStart();
			
			// suppression de l'entrainement
			$query = $db->getQuery(true);
			
			$conditions = $db->quoteName('id') . ' =  ' . $perso->getId();
			$query->update($db->quoteName('persos'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$result = $db->execute();
			
			// ajout de la nouvelle compétence
			$query = $db->getQuery(true);
			$date = JFactory::getDate()->format('Y-m-d');
			$columns = array('id_perso', 'competence_id', 'date_acquisition', 'xp_used');
			
			$values = array($perso->getId(), $competenceId, $db->quote($date), $db->quote($descXpUsed));
			 
			// Prepare the insert query.
			$query
			    ->insert($db->quoteName('persos_competences'))
			    ->columns($db->quoteName($columns))
			    ->values(implode(',', $values));
			    
			$db->setQuery($query);
			$db->execute();
			$db->transactionCommit();
		}
		catch (Exception $e)
		{
		   // catch any database errors.
		   $db->transactionRollback();
		   $error = 1;
			$msg = "Erreur lors des changements en base de données lors de l'aprentissage de la compétence";
		}
		return array("error" => $error, "msg" => $msg);
	}
	
}

?>
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
	
	public static function getPersoIdFromUser($userId) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$columns = 'perso_id';	
		$query->select($db->quoteName($columns));
		$query->from($db->quoteName('persos_users'));
		$query->where($db->quoteName('user_id') . ' = '. $userId);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$db->setQuery($query);
		$result = $db->loadResult();
 		
 		if($result === null) {
 			return -1;
 		}
 		return $result;
	}
	
	public static function associateUserPerso($persoId, $userId) {
		$data = array("error" => 0, "msg" => "Association effectuée");
		if(getPersoIdFromUser($userId) != -1)	{
			$data['error'] = 1;
			$data['msg'] = 'Cet utilisateur est déjà associé à un personnage';
			return $data;
		}
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$columns = array('perso_id', 'user_id');
		$values = array($persoId, $userId);
		 
		// Prepare the insert query.
		$query
		    ->insert($db->quoteName('persos_users'))
		    ->columns($db->quoteName($columns))
		    ->values(implode(',', $values));
		    
		$db->setQuery($query);
		$db->execute();
		
		return $data;
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
	
	
	private static function updateEntrainements($persoId, $entrainements) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$field = $db->quoteName('entrainements') . ' = ' . $db->quote(json_encode($entrainements));
		
		$conditions = $db->quoteName('id') . ' =  ' . $persoId;
		$query->update($db->quoteName('persos'))->set($field)->where($conditions);
		$db->setQuery($query);
		$db->execute();
	}
	
	private static function updatePointsCreation($persoId, $newPcVal) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$field = $db->quoteName('points_creation') . ' = ' . $newPcVal;
		
		$conditions = $db->quoteName('id') . ' =  ' . $persoId;
		$query->update($db->quoteName('persos'))->set($field)->where($conditions);
		$db->setQuery($query);
		$db->execute();
	}
	
	private static function addCompetenceToPerso($perso, $competenceId, $xpUsed) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$date = JFactory::getDate()->format('Y-m-d');
		$columns = array('id_perso', 'competence_id', 'date_acquisition', 'xp_used');
		$values = array($perso->getId(), $competenceId, $db->quote($date), $db->quote($xpUsed));
		 
		// Prepare the insert query.
		$query
		    ->insert($db->quoteName('persos_competences'))
		    ->columns($db->quoteName($columns))
		    ->values(implode(',', $values));
		    
		$db->setQuery($query);
		$db->execute();
	}
	
	public static function apprentissageGratuit($perso, $competenceId) {
		$error = 0;
		$msg = "";
		$xpUsed = json_encode(array("gratuit" => ''));
		
		try
		{
			// ajout de la nouvelle compétence			 
			PersoHelper::addCompetenceToPerso($perso, $competenceId, $xpUsed);
		}
		catch (Exception $e)
		{
		   $error = 1;
			$msg = "Erreur lors des changements en base de données lors de l'aprentissage de la compétence";
		}
		return array("error" => $error, "msg" => $msg);
	}
	
	
	public static function useEntrainement($entrainementId, $competenceId, $perso) {
		$error = 0;
		$msg = "";
		$db = JFactory::getDbo();
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data)
		$entrainements = $perso->getXp()->getEntrainements();
		unset($entrainements[$entrainementId]);
		$xpUsed = json_encode(array("entrainement" => $entrainementId));
		
		try
		{
			$db->transactionStart();
			
			// suppression de l'entrainement
			PersoHelper::updateEntrainements($perso->getId(), $entrainements);
			
			// ajout de la nouvelle compétence
			PersoHelper::addCompetenceToPerso($perso, $competenceId, $xpUsed);
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
		$xp = $perso->getXp();
		foreach(ClasseXP::getTypesCristaux() as $type) {
			if($xpUsed->getCristaux($type) > 0) {
				$newVal = $xp->getCristaux($type) - $xpUsed->getCristaux($type);
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
			PersoHelper::addCompetenceToPerso($perso, $competenceId, $descXpUsed);
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
	
	public static function usePointsCreation($pcUsed, $competenceId, $perso) {
		$error = 0;
		$msg = "";
		$db = JFactory::getDbo();
		
		$newVal = $perso->getXp()->getPointsCreation() - $pcUsed;
		$field = $db->quoteName('points_creation'). ' = ' .$newVal;
		$xpUsed = json_encode(array("points_creation" => $pcUsed));
		
		try
		{
			$db->transactionStart();
			
			// suppression de l'entrainement
			$query = $db->getQuery(true);
			
			$conditions = $db->quoteName('id') . ' =  ' . $perso->getId();
			$query->update($db->quoteName('persos'))->set($field)->where($conditions);
			$db->setQuery($query);
			$result = $db->execute();
			
			// ajout de la nouvelle compétence
			$query = $db->getQuery(true);
			$date = JFactory::getDate()->format('Y-m-d');
			$columns = array('id_perso', 'competence_id', 'date_acquisition', 'xp_used');
			
			$values = array($perso->getId(), $competenceId, $db->quote($date), $db->quote($xpUsed));
			 
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
	
	
	private static function getXpUsedForCompetence($perso, $competenceId) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
			
		$conditions = $db->quoteName('id_perso') . ' =  ' . $perso->getId();
		$conditions = $conditions . ' AND ' . $db->quoteName('competence_id') . ' =  ' . $competenceId;
		$query->select($db->quoteName('xp_used'));
		$query->from($db->quoteName('persos_competences'));
		$query->where($conditions);
		$db->setQuery($query);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		return $db->loadAssoc();
	}
	
	private static function getNomCompetence($competenceId) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('competence_nom'));
		$query->from($db->quoteName('competences'));
		$query->where($db->quoteName('competence_id') . ' = '.$competenceId);
		$db->setQuery($query);
		
		return $db->loadResult();
	}
	
	private static function removeCompetence($persoId, $competenceId) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		 
		// delete all custom keys for user 1001.
		$conditions = array(
		    $db->quoteName('id_perso') . ' = ' . $persoId, 
		    $db->quoteName('competence_id') . ' = ' . $competenceId
		);
		 
		$query->delete($db->quoteName('persos_competences'));
		$query->where($conditions);
		error_log($query->__toString());
		 
		$db->setQuery($query);
		 
		$result = $db->execute();		
	}	
	
	public static function forgetCompetence($competenceId, $perso) {
		$data = array("error" => 0, "msg" => "Compétence supprimée");
		$db = JFactory::getDbo();
		$results = PersoHelper::getXpUsedForCompetence($perso, $competenceId);
		$xp = $perso->getXp();
 		
 		if(count($results) == 0) {
 			$data["error"] = 1;
			$data["msg"] = "Cette compétence n'a pas été trouvée associée au personnage";
 		}
		
		try
		{
			$db->transactionStart();
			
			// récupération de l'xp dépensée lors de l'apprentissage
			if($data["error"] == 0) {
				$xpUsed = json_decode($results['xp_used'], true);
				
				switch( array_keys($xpUsed)[0] ) {
					case 'gratuit':
						PersoHelper::removeCompetence($perso->getId(), $competenceId);
						break;
										
					
					case 'points_creation':
						$pc = $xp->getPointsCreation();
						$pcUsed = $xpUsed['points_creation'];
						PersoHelper::updatePointsCreation($perso->getId(), $pc + $pcUsed);
						PersoHelper::removeCompetence($perso->getId(), $competenceId);
						break;
					
					case 'entrainement':
						$entrainements = $xp->getEntrainements();
						$entrainementId = $xpUsed['entrainement'];
						$nomEntrainement = PersoHelper::getNomCompetence($entrainementId);
						$xp->addEntrainement($entrainementId, $nomEntrainement);
						PersoHelper::updateEntrainements($perso->getId(), $xp->getEntrainements());
						PersoHelper::removeCompetence($perso->getId(), $competenceId);
						break;
						
					case 'cristaux':
						$fields = array();
						foreach($xpUsed['cristaux'] as $type => $val) {
							$newVal = $xp->getCristaux($type) + $val;
							array_push($fields, $db->quoteName('cristaux_'.$type). ' = ' .$newVal);
						}						
						
						$query = $db->getQuery(true);
						
						$conditions = $db->quoteName('id') . ' =  ' . $perso->getId();
						$query->update($db->quoteName('persos'))->set($fields)->where($conditions);
						$db->setQuery($query);
						$result = $db->execute();
						PersoHelper::removeCompetence($perso->getId(), $competenceId);
						break;
						
					default:
						error_log("Méthode d'aquisition de compétence inconnue : ".print_r($xpUsed, true));
				}
			}
			
			$db->transactionCommit();
		}
		catch (Exception $e)
		{
		   // catch any database errors.
		   $db->transactionRollback();
		   $data["error"] = 1;
			$data["msg"] = "Erreur lors des changements en base de données pour la suppression de la compétence";
		}
		
		if($data["error"] == 0) {
			$perso->deleteCompetence($competenceId);
			$competencesAcquises = array_keys($perso->getCompetences());
			$data["competences"] = $competencesAcquises;
		}
		return $data;
	}
	
}

?>
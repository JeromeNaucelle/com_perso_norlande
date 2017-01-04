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
require_once JPATH_COMPONENT . '/includes/ClasseXP.php';
/**
 * Hello World Component Controller
 *
 * @since  0.0.1
 */
class Perso_NorlandeController extends JControllerLegacy
{
	
	public function getArbreMaitrise() 
	{	
		$mainframe = JFactory::getApplication();
		
		$model = null;
		$model = $this->getModel('creationperso');
		
		$jinput = JFactory::getApplication()->input;
		$competence_id = $jinput->get('competence', '0', 'INT');
		$arbre_maitrise = $model->getArbreMaitrise($competence_id);
		
		//TODO : enlever ça, ici seulement pour les tests
		$perso = $model->getPerso('firstPerso');
		if($perso == null) {
			JLog::add(JText::_('Perso non trouvé'), JLog::WARNING, 'jerror');		
		}
		// fin TODO
		
		$data = array("arbre" => $arbre_maitrise, "competences_acquises" => array_keys($perso->getCompetences()) );
		
		echo json_encode($data);  
		$mainframe->close();
	}
	
	
	public function initEntraineurs() {
		$mainframe = JFactory::getApplication();
		$model = null;
		$model = $this->getModel('creationperso');
		$model->initEntraineurs();
		echo "Creation des entraineurs effectue";
		$mainframe->close();
	}
	
	public function userSelect()
	{
		$mainframe = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$data = array("result" => "competence ID invalide");
		$competence_id = $jinput->get('competence', '0', 'INT');
		if($competence_id == 0)
		{
			echo json_encode($data);
			$mainframe->close();
			return;
		}
		
		
		$session = JFactory::getSession();
		$perso = unserialize($session->get( 'perso', 'empty' ));
		if($perso === 'empty')
		{
			$data["result"] = "Personnage non trouvé dans la session";
		}
	public function updateDetailsPerso() {
		$mainframe = JFactory::getApplication();
		
		$model = null;
		$model = $this->getModel('detailsperso');
		
		$jinput = JFactory::getApplication()->input;
		
		$cristaux = array();
		foreach(ClasseXP::get_types_cristaux() as $type) {
			$cristaux['cristaux_'.$type] = $jinput->get('cristaux_'.$type, '0', 'INT');
		}
		error_log(var_dump($cristaux));
		//TODO : enlever ça, ici seulement pour les tests
		$perso = $model->getPerso('firstPerso');
		if($perso == null) {
			JLog::add(JText::_('Perso non trouvé'), JLog::WARNING, 'jerror');		
		}
		// fin TODO
		$model->setCristaux($cristaux, $perso);
		
		//echo json_encode($data);  
		$mainframe->redirect('index.php?option=com_perso_norlande&view=detailsperso');
	}
	
	public function searchEntrainement() {
		$mainframe = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
 
		//recherche des résultats dans la base de données

		$term = $jinput->get('term', '0', 'STR');
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		
		$query
		->select('a.*')
		->from($db->quoteName('competences', 'a'))
		->join('INNER', $db->quoteName('competences', 'b') . ' ON (' . $db->quoteName('a.competence_id') . ' = ' . $db->quoteName('b.parent_id') . ')')
		->where($db->quoteName('b.entraineur') . ' = 1 AND '.$db->quoteName('b.competence_nom').' LIKE '.$db->quote('%'.$term.'%'))
		->setLimit(10);
				
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
				 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadAssocList();
		$array = array();
		 
		// affichage d'un message "pas de résultats"
		if( count( $results ) == 0 )
		{
				array_push($array, "Pas de r&eacute;sultats pour cette recherche");
		}
		else
		{
			foreach($results as $key => $item) {
				array_push($array, array('label' => $item['competence_nom'], 'value'=> $item['competence_id']));
			}
		}
		echo json_encode($array);
		$mainframe->close();
	}
	
	public function searchPerso() {
		$mainframe = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
 
		//recherche des résultats dans la base de données

		$term = $jinput->get('term', '0', 'STR');
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
		$results = $db->loadAssocList();
		$array = array();
		 
		// affichage d'un message "pas de résultats"
		if( count( $results ) == 0 )
		{
				array_push($array, "Pas de r&eacute;sultats pour cette recherche");
		}
		else
		{
			foreach($results as $key => $item) {
				array_push($array, array('label' => $item['nom'].' ('.$item['lignee'].')', 'value'=> $item['id']));
			}
		}
		echo json_encode($array);
		$mainframe->close();
	}
	
	public function selectPerso() {
		$mainframe = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
 		$perso_id = $jinput->get('perso_id', '0', 'STR');
 		
 		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$columns = array('id','nom','lignee');	
		$query->select($db->quoteName($columns));
		$query->from($db->quoteName('persos'));
		$query->where($db->quoteName('id') . ' = '. $perso_id);
		error_log("test : ".$query->__toString());
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$db->setQuery($query);
		$results = $db->loadAssoc();
		error_log("test : ".print_r($results, true));
 		
 		if(count($results) == 0) {
 			JLog::add(JText::_("Personnage non trouvé pour l'id ".$perso_id), JLog::WARNING, 'jerror');	
 		} else {
 			$session = JFactory::getSession();
			$session->set( 'perso_id', $perso_id );
			$session->set( 'perso_nom', $results['nom'] );
		}
		
		$mainframe->redirect('index.php?option=com_perso_norlande&view=detailsperso');
	}
	
	public function addEntrainement() {
		error_log("controller addEntrainement");
		$mainframe = JFactory::getApplication();
		$model = null;
		$model = $this->getModel('detailsperso');		
		
		$jinput = JFactory::getApplication()->input;
 
		//recherche des résultats dans la base de données

		$competence_id = $jinput->get('competence_id', '0', 'STR');
		error_log("competence_id : ".$competence_id);
		
		//TODO : enlever ça, ici seulement pour les tests
		$perso = $model->getPerso('firstPerso');
		if($perso == null) {
			JLog::add(JText::_('Perso non trouvé'), JLog::WARNING, 'jerror');		
		}
		// fin TODO

		$result = $model->addEntrainement($perso, $competence_id);
		error_log("addEntrainement model result : ".json_encode($result));
		echo json_encode($result);
		$mainframe->close();
	}
	
	public function deleteEntrainement() {
		error_log("controller deleteEntrainement");
		$mainframe = JFactory::getApplication();
		$model = null;
		$model = $this->getModel('detailsperso');		
		
		$jinput = JFactory::getApplication()->input;
 
		//recherche des résultats dans la base de données

		$competence_id = $jinput->get('competence_id', '0', 'STR');
		error_log("competence_id : ".$competence_id);
		
		//TODO : enlever ça, ici seulement pour les tests
		$perso = $model->getPerso('firstPerso');
		if($perso == null) {
			JLog::add(JText::_('Perso non trouvé'), JLog::WARNING, 'jerror');		
		}
		// fin TODO

		 
		echo json_encode($model->deleteEntrainement($perso, $competence_id));
		$mainframe->close();
	}	
	
	public function createPerso() {
		error_log("createPerso");
		$error = 0;
		$msg_error = "";
		
		$mainframe = JFactory::getApplication();
		$model = null;
		$model = $this->getModel('detailsperso');
		
		$jinput = JFactory::getApplication()->input;
 
		//recherche des résultats dans la base de données

		$lignee_id = $jinput->get('lignee_perso', -1, 'INT');
		if(array_key_exists($lignee_id, Lignees::$lignees)) {
			$lignee = Lignees::$lignees[$lignee_id];
		} else {
			$error = 1;
			$msg_error = "Lignée inconnue";
		}
		
		$nom = $jinput->get('nom_perso', "", 'STR');
		if($nom === "") {
			$error = 2;
			$msg_error = "Il faut renseigner le nom du personnage";
		}
		
		if($error === 0) {
			try {
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$columns = array('nom', 'lignee');
				$values = array($db->quote($nom), $db->quote($lignee));
				 
				// Prepare the insert query.
				$query
				    ->insert($db->quoteName('persos'))
				    ->columns($db->quoteName($columns))
				    ->values(implode(',', $values));
				    
				$db->setQuery($query);
				$db->execute();
				$newId = $db->insertid();
			} catch(Exception $e) {
				$error = 3;
				$msg_error = "Erreur lors de l'insertion d'un perso en BDD";
				error_log("Erreur lors de l'insertion d'un perso en BDD : ".$e);
			}
		}
		
		if($error === 0) {
			//TODO placer le $newId dans la session
		}

		$mainframe->redirect('index.php?option=com_perso_norlande&view=detailsperso');
	}	
}

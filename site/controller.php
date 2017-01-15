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
require_once JPATH_COMPONENT . '/helpers/PersoHelper.php';
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
		
		$perso = $this->getCurrentPerso();
		$competencesAcquises = array();
		if($perso != NULL) {
			$competencesAcquises = $perso->getCompetences();
		}
		
		$data = array("arbre" => $arbre_maitrise, "competences_acquises" => array_keys($competencesAcquises) );
		
		echo json_encode($data);  
		$mainframe->close();
	}
	
	private function getIntFromJForm($name, $default) {
		$ret = $default;
		$input = JFactory::getApplication()->input;
		$formData = new JInput($input->get('jform', '', 'array')); 
		$tmp = $formData->get($name, NULL, 'STR');
		if($tmp != NULL) {
			if(preg_match('/^\d+$/',$tmp)) {
			  $ret = (int)$tmp;
			}
		}
		return $ret;
	}
	
	public function updateMonnaie() {
		$data = array("error"=>0, "msg"=>"Données mises à jour");
		$formResult = array();
		$mainframe = JFactory::getApplication();
		$session = JFactory::getSession();
		
		$persoId = $session->get( 'perso_id', NULL );
		
		if($persoId == NULL) {
			$data["msg"] = "Personnage non trouvé dans la session";
			$data["error"] = 1;
		}
		
		if($data["error"] == 0) {
			$model = $this->getModel('detailsperso');
			$tablePerso = $model->getTable();
			
			$formResult['id'] = $persoId;
			foreach ($model->getForm()->getFieldset('monnaie') as $field) {
				$fieldName = $field->getAttribute('name');
				$val = $this->getIntFromJForm($fieldName, -1);
				if($val != -1) {
					$formResult[$fieldName] = $val;
				} else {
					$data['msg'] = "Attention, certaine valeur sont invalides et 
							n'ont donc pas été mises à jour";
				}
			}
			
			$tablePerso->bind($formResult);
			if( !$tablePerso->store() ) {
				$data["msg"] = "Erreur lors de la sauvegarde en base de données";
				$data["error"] = 1;
			}
		}
		
		echo json_encode($data);
		$mainframe->close();
	}
	
	
	// Méthode à appeler une fois après le chargement des mairises
	// dans la BDD pour créer les compétences d'entraineur
	public function initEntraineurs() {
		// TODO : vérifier si ce n'est pas déjà fait
		// TODO : check autorisation		
		
		$mainframe = JFactory::getApplication();
		$model = null;
		$model = $this->getModel('creationperso');
		$model->initEntraineurs();
		echo "Creation des entraineurs effectue";
		$mainframe->close();
	}
	
	private function enoughXp($xpForCompetence, $niveau) {
		
		if( array_key_exists('entrainement', $xpForCompetence) ) {
			return true;
		}
		if(array_key_exists('cristaux', $xpForCompetence)) {
			$cristaux = 0;
			foreach($xpForCompetence['cristaux'] as $type => $val) {
				$cristaux += $val;
			}
			if($cristaux >= $niveau) {
				return true;
			}
		}
		return false;
	}
	
	// Méthode appelée lorsqu'un utilisateur sélectionne
	// une compétence d'un arbre de maitrise
	public function userSelect()
	{
		$error = 0;
		$perso = null;
		$mainframe = JFactory::getApplication();
		$session = JFactory::getSession();
		$jinput = JFactory::getApplication()->input;
		$data = array("result" => -1, "msg" => "erreur inconnue");
		
		$competence_id = $jinput->get('competence', -1, 'INT');
		if($competence_id == -1)
		{
			$error = 1;
			$data["msg"] = "competence ID invalide (".$competence_id.")";
		}
		
		if($error === 0)
		{
			error_log("test1");
			$perso = $this->getCurrentPerso();
			if($perso == NULL)
			{
				error_log("test2");
				$data["result"] = -1;
				$data["msg"] = "Personnage non trouvé dans la session";
				$error = 1;
			}
		}
		
		$model = null;
		$result = null;
		if($error === 0)
		{
			error_log("test3");
			$model = $this->getModel('creationperso');
			
			// TODO check l'existance de competence_id
			$arbre = $model->getArbreMaitrisePhp($competence_id);
			$competence = $arbre->getCompetence($competence_id);
			
			$result = $perso->canLearn($competence_id, $arbre);
			error_log("dump : ".print_r($result,true));
			
			if($result["result"] === 1)
			{
				error_log("test4");
				$data["result"] = 2;
				$xpForCompetence = $perso->getXpForCompetence($competence_id, $arbre);
				
				if( $this->enoughXp($xpForCompetence, $competence->getNiveau()) ) {
					$data["xp"] = $xpForCompetence;
					$data["niveauCompetence"] = $competence->getNiveau();
					
					$session->set( 'competenceToLearn', $competence_id );
					$session->set( 'niveauCompetence', $competence->getNiveau() );
				}	else {
					$data["result"] = -1;
					$data["msg"] = "Vous n'avez pas acquis suffisament d'expérience pour pouvoir apprendre cette compétence";
				}
				
				// On ajoute la nouvelle compétence au Perso
				// TODO : insérer la vérification des droits orga
				
				//$data["competences"] = array_merge($data['competences'], array_keys($perso->getCompetences()));
			} else if($result["result"] === 2)
			{
				error_log("test5");
				//pré-requis à vérifier
				$data["result"] = -1;
				$data["msg"] = "Il est nécessaire d'acquérir les compétences précédentes dans cette branche";
			} else {
				$data["result"] = -1;
				$data["msg"] = $result['msg'];
			}
		}

		echo json_encode($data);
		$mainframe->close();
	}
	

	
	public function userChoiceDepenseXP()
	{
		$error = 0;
		$mainframe = JFactory::getApplication();
		$session = JFactory::getSession();
		$jinput = JFactory::getApplication()->input;
		$data = array("error" => 0, "msg" => "erreur inconnue");
		
		$competenceId = $session->get('competenceToLearn');
		$typeXp = $jinput->get('typeXp', 'err', 'STR');
		if($competenceId == 0)
		{
			$data["error"] = 1;
			$data['msg'] = "Erreur : identifiant de compétence non trouvé";
		}
		
		if($data["error"] === 0)
		{
			$perso = $this->getCurrentPerso();
			if($perso === NULL)
			{
				$data["msg"] = "Personnage non trouvé dans la session";
				$data["error"] = 1;
			}
		}
		
		if($data["error"] === 0)
		{
			switch($typeXp) {
				case 'entrainement':
					$data = $this->depenseEntrainement($perso, $competenceId);
					break;
					
				case 'cristaux':
					$data = $this->depenseCristaux($perso, $competenceId);
					break;
					
				default:
					$data["msg"] = "Utilisation de l'XP inconnue";
					$data["error"] = 1;
			}
		}
		
		if($data["error"] === 0)
		{
			// On envoie la liste des compétences acquises dans cette
			// branche pour le rendu graphique
			$competencesAcquises = array_keys($perso->getCompetences());
			array_push($competencesAcquises , $competenceId);
			$data["competences"] = $competencesAcquises;
		}
		echo json_encode($data);
		$mainframe->close();
	}
	
	private function depenseEntrainement($perso, $competenceId) {
		$db = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;
		$data = array("error" => 0, "msg" => "erreur inconnue");
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data)
		$entrainements = $perso->getXp()->getEntrainements();
		$entrainementId = $jinput->get('dep_entrainement_group', -1, 'INT');
		
		if( $entrainementId == -1 ) {
			$data["error"] = 1;
			$data["msg"] = "Aucun entrainement sélectionné";
		}
		
		$choosenEntrainement = 0;
		if($data["error"] == 0) {
			if( !array_key_exists($entrainementId, $entrainements) ) {
				$data["error"] = 1;
				$data["msg"] = "L'entrainement sélectionné n'a pas été suivis par le personnage";
			}
		}
		
		if($data["error"] == 0) {
			$data = PersoHelper::useEntrainement($entrainementId, $competenceId, $perso);	
		}
		
		return $data;
	}
	
	private function depenseCristaux($perso, $competenceId) {
		$db = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;
		$session = JFactory::getSession();
		
		$data = array("error" => 0, "msg" => "erreur inconnue");
		$niveauCompetence = $session->get( 'niveauCompetence', -1 );
		if($niveauCompetence == -1) {
			$data["error"] = 1;
			$data["msg"] = "Niveau de la compétence non stocké dans la session";
		}
		
		if($data["error"] == 0) {
			$cristauxDep = 0;
			$xpUsed = new ClasseXP();
			foreach(ClasseXP::getTypesCristaux() as $type) {
				$tmp = $jinput->get('dep_cristaux_'.$type, -1, 'UINT');
				if($tmp != -1 
						&& $perso->getXp()->getCristaux($type) >= $tmp) {
					$cristauxDep += $tmp;
					$xpUsed->setCristaux($type, $tmp);
				}
			}
			if($cristauxDep != $niveauCompetence) {
				$data["error"] = 1;
				$data["msg"] = "Le nombre de cristaux dépensés est incohérent avec le niveau de la compétence";
			}
		}
		
		
		if( $data["error"] == 0 ) {
			$data = PersoHelper::useCristaux($xpUsed, $competenceId, $perso);	
		}
		
		return $data;
	}
	
	private function getInt($name, $default) {
		$ret = $default;
		$jinput = JFactory::getApplication()->input;
		$tmp = $jinput->get($name, NULL, 'STR');
		if($tmp != NULL) {
			if(preg_match('/^\d+$/',$tmp)) {
			  $ret = (int)$tmp;
			}
		}
		return $ret;
	}
	
	public function updatePointsCreationPerso() {
		$mainframe = JFactory::getApplication();
		
		$pointsCreation = $this->getInt('pointsCreation', -1);
		$data = array("error"=>0, "msg"=>"");
		
		$perso = $this->getCurrentPerso();
		if($perso === NULL)
		{
			$data["msg"] = "Personnage non trouvé dans la session";
			$data["error"] = 1;
		}
		
		if($data["error"] === 0)
		{
			$data['pointsCreation'] = $perso->getXp()->getPointsCreation();
			if($pointsCreation == -1) {
				$data['error'] = 1;
				$data['msg'] =  "La valeur entrée n'est pas un chiffre valide";
			}
		}
		
		if($data["error"] === 0)
		{
			$model = $this->getModel('detailsperso');
			if($model->setPointsCreation($pointsCreation, $perso) != 1) {
				$data['error'] = 1;
				$data['msg'] =  "Erreur lors du changement en BDD";
			}
		}
		
		if($data["error"] === 0)
		{
			$data['pointsCreation'] = $pointsCreation;
			$data['msg'] =  "Données mises à jour";
		}
		
		
		echo json_encode($data);
		$mainframe->close();
	}
	
	public function updateCristauxPerso() {
		// TODO : check orga	
		// TODO : check que les inputs soient bien des chiffres		
		
		$mainframe = JFactory::getApplication();
		
		$model = null;
		$model = $this->getModel('detailsperso');
		
		$jinput = JFactory::getApplication()->input;
		
		$cristaux = array();
		foreach(ClasseXP::getTypesCristaux() as $type) {
			$cristaux['cristaux_'.$type] = $jinput->get('cristaux_'.$type, '0', 'INT');
		}
		$perso = $this->getCurrentPerso();
		$model->setCristaux($cristaux, $perso);
		
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
 
		//recherche des résultats dans la base de données
		$term = $jinput->get('term', '0', 'STR');
				 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = PersoHelper::searchPersoByName($term);
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
	
	private function setCurrentPerso($perso_id) {
		// TODO : check orga	ou joueur autorisé
 		$test = PersoHelper::persoExists($perso_id);
 		if($test === false) {
 			JLog::add(JText::_("Personnage non trouvé pour l'id ".$perso_id), JLog::WARNING, 'jerror');	
 		} else {
 			$session = JFactory::getSession();
			$session->set( 'perso_id', $perso_id );
			$session->set( 'perso_nom', $test );
		}
	}
	
	private function getCurrentPerso() {
		$perso = NULL;
		$session = JFactory::getSession();
		$id = $session->get( 'perso_id', NULL );
		
		if($id == NULL) {
			JLog::add(JText::_("Aucun personnage sélectionné actuellement"), JLog::ERROR, 'jerror');
		} else {
			$perso = PersoHelper::getPersoById($id);
		}
		return $perso;
	}
	
	public function selectPerso() {
		$mainframe = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
 		$perso_id = $jinput->get('perso_id', '0', 'STR');
 		
 		$this->setCurrentPerso($perso_id);
		
		$mainframe->redirect('index.php?option=com_perso_norlande&view=detailsperso');
	}
	
	public function addEntrainement() {
		// TODO : check orga	
		
		error_log("controller addEntrainement");
		$mainframe = JFactory::getApplication();
		$model = null;
		$model = $this->getModel('detailsperso');		
		
		$jinput = JFactory::getApplication()->input;
 
		//recherche des résultats dans la base de données

		$competence_id = $jinput->get('competence_id', '0', 'STR');
		
		$perso = $this->getCurrentPerso();

		$result = $model->addEntrainement($perso, $competence_id);
		echo json_encode($result);
		$mainframe->close();
	}
	
	
	// Méthode appelée lorsqu'un orga supprime un entrainement
	// de la liste des entrainements acquis d'un perso
	// (AJAX)
	public function deleteEntrainement() {
		// TODO : check orga		
		
		$model = null;
		$model = $this->getModel('detailsperso');	
		$mainframe = JFactory::getApplication();		
		$jinput = JFactory::getApplication()->input;
 
		//recherche des résultats dans la base de données

		$competence_id = $jinput->get('competence_id', '0', 'STR');
		error_log("competence_id : ".$competence_id);
		
		$perso = $this->getCurrentPerso();
		 
		echo json_encode($model->deleteEntrainement($perso, $competence_id));
		$mainframe->close();
	}	
	
	public function createPerso() {
		$error = 0;
		$msg_error = "";
		
		$mainframe = JFactory::getApplication();		
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
				$newId = PersoHelper::insertPerso($nom, $lignee);
				$this->setCurrentPerso($newId);
			} catch(Exception $e) {
				$error = 3;
				$msg_error = "Erreur lors de l'insertion d'un perso en BDD";
				error_log("Erreur lors de l'insertion d'un perso en BDD : ".$e);
			}
		}

		$mainframe->redirect('index.php?option=com_perso_norlande&view=detailsperso');
	}	
}

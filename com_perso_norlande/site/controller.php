<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/includes/Perso.php';
require_once JPATH_COMPONENT . '/includes/ClasseXP.php';
require_once JPATH_COMPONENT . '/helpers/CommonHelper.php';
require_once JPATH_COMPONENT . '/helpers/PersoHelper.php';
require_once JPATH_COMPONENT . '/helpers/QuestionDepenseHelper.php';
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
		$user = JFactory::getUser();
		
		$edit_orga = $user->authorise('core.edit_orga', 'com_perso_norlande');
		
		if(!$edit_orga) {
			$data["msg"] = "Vous ne disposez pas des droits nécessaires";
			$data["error"] = 1;
		}
		
		if($data["error"] == 0) {
			$persoId = $session->get( 'perso_id', -1 );
			
			if($persoId == -1) {
				$data["msg"] = "Personnage non trouvé dans la session";
				$data["error"] = 1;
			}
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
	
	
	public function deletePerso() {
		$data = array("error"=>0, "msg"=>"Personnage supprimé");
		$mainframe = JFactory::getApplication();
		$session = JFactory::getSession();
		
		$persoId = $session->get( 'perso_id', -1 );
			
		if($persoId == -1) {
			$data["msg"] = "Personnage non trouvé dans la session. Veuillez contacter un administrateur.";
			$data["error"] = 1;
		}
		
		if($data["error"] == 0) {
			$data = PersoHelper::deletePerso($persoId);
		}
		
		if($data["error"] == 0) {
			$session->clear('perso_id');
			$mainframe->redirect('index.php?option=com_perso_norlande&view=detailsperso');
		} else {
			error_log($data["msg"]);
			//JError::raiseError( 500, $data["msg"] );
			$mainframe->enqueueMessage($data["msg"], 'error');
			//echo json_encode($data);
			//$mainframe->close();
		}
		$mainframe->redirect('index.php?option=com_perso_norlande&view=detailsperso');
	}
	
	
	public function updateArmure() {
		$data = array("error"=>0, "msg"=>"Données mises à jour");
		$formResult = array();
		$mainframe = JFactory::getApplication();
		$session = JFactory::getSession();
		$user = JFactory::getUser();
		$jinput = $mainframe->input;
		$armure = '';
		
		$edit_orga = $user->authorise('core.edit_orga', 'com_perso_norlande');
		
		if($data["error"] === 0
			&& !$edit_orga)
		{
			$validation_user = $perso->userHasValidate();
			if($validation_user == true)
			{
				$data["error"] = 1;
				$data["msg"] = "Vous avez déjà validé votre personnage, seul un orga peut encore le modifier";
			}
		}
		
		
		if($data["error"] == 0) {
			$persoId = $session->get( 'perso_id', -1 );
			
			if($persoId == -1) {
				$data["msg"] = "Personnage non trouvé dans la session";
				$data["error"] = 1;
			}
		}
		
		if($data["error"] == 0) {
			$armure = $jinput->get('armure', '', 'STR');
			
			$armureValues = CommonHelper::getEnumValues( 'persos', 'armure' );
			$found = false;
			foreach($armureValues as $type) {
				if($type == $armure) {
					$found = true;
					break;	
				}
			}
			if($found == false) {
				$data["msg"] = "Type d'armure inconnu";
				$data["error"] = 1;
			}
		}
		
		if($data["error"] == 0) {
			PersoHelper::updateArmure($persoId, $armure);
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
		if(array_key_exists('points_creation', $xpForCompetence)) {
			$pc = $xpForCompetence['points_creation'];
			if($pc >= $niveau) {
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
		$jinput = $mainframe->input;
		$user = JFactory::getUser();
		$edit_orga = $user->authorise('core.edit_orga', 'com_perso_norlande');
		
		$data = array("result" => -1, "msg" => "erreur inconnue");
		
		$competence_id = $this->getInt('competence', -1);
		if($competence_id == -1)
		{
			$error = 1;
			$data["msg"] = "competence ID invalide (".$competence_id.")";
		}
		
		if($error === 0)
		{
			// error_log("userSelect 1");
			$perso = $this->getCurrentPerso();
			if($perso == NULL)
			{
				error_log("userSelect : Personnage non trouvé dans la session");
				$data["result"] = -1;
				$data["msg"] = "Personnage non trouvé dans la session";
				$error = 1;
			}
		}
		
		$model = null;
		$result = null;
		// error_log("userSelect 2");
		if($error === 0)
		{
			$model = $this->getModel('creationperso');
			
			// TODO check l'existance de competence_id
			// error_log("userSelect 3");
			$arbre = $model->getArbreMaitrisePhp($competence_id);
			$competence = $arbre->getCompetence($competence_id);
			error_log("userSelect 4");
			// TODO voir si la vérification de orga/joueur ne pourrait pas se faire
			// dans le controleur pour une meilleur homogénéité du code
			$result = $perso->canLearn($competence_id, $arbre, $edit_orga);
			// error_log("userSelect 5");
			if($result["result"] === 1)
			{
				$data["result"] = 2;
				$xpForCompetence = $perso->getXpForCompetence($competence_id, $arbre, $competence->getNiveau());
				
				if( $this->enoughXp($xpForCompetence, $competence->getNiveau()) 
						|| $edit_orga) {
							error_log("userSelect 6");
					$data["xp"] = $xpForCompetence;
					$data["niveauCompetence"] = $competence->getNiveau();
					
					$session->set( 'competenceToLearn', $competence_id );
					$session->set( 'niveauCompetence', $competence->getNiveau() );
				}	else {
					$data["result"] = -1;
					$data["msg"] = "Vous n'avez pas acquis suffisament d'expérience pour pouvoir apprendre cette compétence";
				}
				
			} else if($result["result"] === 2)
			{
				//pré-requis à vérifier
				$data["result"] = -1;
				$data["msg"] = "Il est nécessaire d'acquérir les compétences précédentes dans cette branche";
			} else if($result["result"] === 3) {
				error_log("userSelect : La compétence est déjà acquise");
				
				// compétence déjà acquise, on la désapprend
				$data["result"] = -1;
				$data["msg"] = "La compétence est déjà acquise";
				
			} else {
				$data["result"] = -1;
				$data["msg"] = $result['msg'];
			}
		}

		//error_log("userSelect fin");
		echo QuestionDepenseHelper::getQuestionDepenseXp($data);
		$mainframe->close();
	}
	
	
	public function forgetCompetence() {
		$perso = null;
		$validation_user = true;
		$validation_orga = true;	
		$user = JFactory::getUser();
		$edit_orga = $user->authorise('core.edit_orga', 'com_perso_norlande');	
		
		$mainframe = JFactory::getApplication();
		$data = array("error" => 0, "msg" => "");
		
		$competence_id = $this->getInt('competence', -1);
		if($competence_id == -1)
		{
			$data["error"] = 1;
			$data["msg"] = "competence ID invalide (".$competence_id.")";
		}
		
		if($data["error"] === 0)
		{
			$perso = $this->getCurrentPerso();
			if($perso == NULL)
			{
				$data["error"] = 1;
				$data["msg"] = "Personnage non trouvé dans la session";
			}
		}
		
		if($data["error"] === 0
			&& !$edit_orga)
		{
			$validation_user = $perso->userHasValidate();
			if($validation_user == true)
			{
				$data["error"] = 1;
				$data["msg"] = "Vous avez déjà validé votre personnage, seul un orga peut encore le modifier";
			}
		}
		
		if($data["error"] === 0
				&& !$edit_orga)
		{
			$validation_orga = $perso->isValidatedCompetence($competence_id);
			if($validation_orga == true)
			{
				$data["error"] = 1;
				$data["msg"] = "Cette compétence a été validée par un orga, seul un orga peut la modifier";
			}
		}
		
		if($data["error"] === 0)
		{
			if( !$perso->canForgetCompetence($competence_id) ) {
				$data["error"] = 1;
				$data["msg"] = "Vous devez d'abord oublier les compétences dépendantes de celle-ci";
			}
		}
		
		if($data["error"] === 0) {
			$data = PersoHelper::forgetCompetence($competence_id, $perso);
		}
		echo json_encode($data);
		$mainframe->close();
	}

	
	public function userChoiceDepenseXP()
	{
		$error = 0;
		$mainframe = JFactory::getApplication();
		$session = JFactory::getSession();
		$jinput = $mainframe->input;
		$data = array("error" => 0, "msg" => "erreur inconnue");
		
		$competenceId = $session->get('competenceToLearn', -1);
		$typeXp = $jinput->get('typeXp', 'err', 'STR');
		if($competenceId == -1)
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
			$user = JFactory::getUser();
			$edit_orga = $user->authorise('core.edit_orga', 'com_perso_norlande');
		
			switch($typeXp) {
				case 'entrainement':
					$data = $this->depenseEntrainement($perso, $competenceId);
					break;
					
				case 'cristaux':
					$data = $this->depenseCristaux($perso, $competenceId);
					break;
					
				case 'points_creation':
					$data = $this->depensePointsCreation($perso, $competenceId);
					break;
					
				case 'gratuit':
					if($edit_orga) {
						$data = PersoHelper::apprentissageGratuit($perso, $competenceId);
					} else {
						$data["msg"] = "Vous ne disposez pas des droits nécessaires";
						$data["error"] = 1;
					}
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
		$entrainementId = $this->getInt('dep_entrainement_group', -1);
		
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
	
	private function depensePointsCreation($perso, $competenceId) {
		$db = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;
		$session = JFactory::getSession();
		
		$data = array("error" => 0, "msg" => "erreur inconnue");
		$niveauCompetence = $session->get( 'niveauCompetence', -1 );
		if($niveauCompetence == -1) {
			$data["error"] = 1;
			$data["msg"] = "Niveau de la compétence non stocké dans la session";
		}
		
		/* TODO : check ce qu'il se passe si l'utilisateur rentre -2 en PC dépensés */
		if($data["error"] == 0) {
			$pcDep = 0;
			$tmp = $this->getInt('dep_points_creation', -1);
			if($tmp > -1 
					&& $perso->getXp()->getPointsCreation() >= $tmp) {
				$pcDep = $tmp;
			}
			if($pcDep != $niveauCompetence) {
				$data["error"] = 1;
				$data["msg"] = "Le nombre de point dépensés est incohérent avec le niveau de la compétence";
			}
		}
		
		if( $data["error"] == 0 ) {
			$data = PersoHelper::usePointsCreation($pcDep, $competenceId, $perso);	
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
				$tmp = $this->getInt('dep_cristaux_'.$type, -1);
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
		
		$user = JFactory::getUser();
		$edit_orga = $user->authorise('core.edit_orga', 'com_perso_norlande');
		
		if(!$edit_orga) {
			$data["msg"] = "Vous ne disposez pas des droits nécessaires";
			$data["error"] = 1;
		}
		
		if($data["error"] === 0) {
			$perso = $this->getCurrentPerso();
			if($perso === NULL)
			{
				$data["msg"] = "Personnage non trouvé dans la session";
				$data["error"] = 1;
			}
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
	
	
	public function updateAnciennete() {
		$mainframe = JFactory::getApplication();
		$jinput = $mainframe->input;
		
		$anciennete = $jinput->getInt('anciennete', -1);
		$data = array("error"=>0, "msg"=>"");
		error_log('test updateAnciennete');
		
		$user = JFactory::getUser();
		$edit_orga = $user->authorise('core.edit_orga', 'com_perso_norlande');
		
		if(!$edit_orga) {
			$data["msg"] = "Vous ne disposez pas des droits nécessaires";
			$data["error"] = 1;
		}
		
		if($data["error"] === 0) {
			$perso = $this->getCurrentPerso();
			if($perso === NULL)
			{
				$data["msg"] = "Personnage non trouvé dans la session";
				$data["error"] = 1;
			}
		}
		
		if($data["error"] === 0)
		{
			$data['anciennete'] = $perso->getAnciennete();
			if($anciennete == -1) {
				$data['error'] = 1;
				$data['msg'] =  "La valeur entrée n'est pas un chiffre valide";
			}
		}
		
		if($data["error"] === 0)
		{
			$model = $this->getModel('detailsperso');
			if($model->setAnciennete($anciennete, $perso) != 1) {
				$data['error'] = 1;
				$data['msg'] =  "Erreur lors du changement en BDD";
			}
		}
		
		if($data["error"] === 0)
		{
			$data['anciennete'] = $anciennete;
			$data['msg'] =  "Données mises à jour";
		}
		
		echo json_encode($data);
		$mainframe->close();
	}
	
	public function updateCristauxPerso() {		
		$mainframe = JFactory::getApplication();
		
		$user = JFactory::getUser();
		$edit_orga = $user->authorise('core.edit_orga', 'com_perso_norlande');
		
		if($edit_orga) {
			$model = null;
			$model = $this->getModel('detailsperso');
			
			$jinput = $mainframe->input;
			
			$cristaux = array();
			foreach(ClasseXP::getTypesCristaux() as $type) {
				$cristaux['cristaux_'.$type] = $this->getInt('cristaux_'.$type, 0, 'INT');
			}
			$perso = $this->getCurrentPerso();
			$model->setCristaux($cristaux, $perso);
		}
		
		$mainframe->redirect('index.php?option=com_perso_norlande&view=detailsperso');
	}
	
	/* Retourne le nom de la compétence en enlevant
	la chaine "Entraineur : " pour une meilleure lisibilité
	*/
	private function entrainementName($val) {
		return substr($val, 13);
	}
	
	public function searchEntrainement() {
		$mainframe = JFactory::getApplication();
		$jinput = $mainframe->input;
		$db = JFactory::getDbo();
 
		//recherche des résultats dans la base de données

		$term = $jinput->get('term', '0', 'STR');
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		
		$query
		->select('*')
		->from($db->quoteName('competences'))
		->where($db->quoteName('entraineur') . ' = 1 AND '.$db->quoteName('competence_nom').' LIKE '.$db->quote('%'.$term.'%'))
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
				$nomEntrainement = $this->entrainementName($item['competence_nom']);
				array_push($array, array('label' => $nomEntrainement, 'value'=> $item['competence_id']));
			}
		}
		echo json_encode($array);
		$mainframe->close();
	}
	
	public function searchPerso() {
		$mainframe = JFactory::getApplication();
		$jinput = $mainframe->input;
 
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
	
	public function searchUser() {
		$mainframe = JFactory::getApplication();
		$jinput = $mainframe->input;
 
		//recherche des résultats dans la base de données
		$term = $jinput->get('term', '0', 'STR');
				 
		$db = JFactory::getDbo();
 
		//recherche des résultats dans la base de données
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$columns = array('id','name','username');		
		
		$query
		->select($db->quoteName($columns))
		->from($db->quoteName('#__users'))
		->where($db->quoteName('name').' LIKE '.$db->quote("%${term}%"))
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
				array_push($array, array('label' => $item['name'].' ('.$item['username'].')', 'value'=> $item['id']));
			}
		}
		echo json_encode($array);
		$mainframe->close();
	}
	
	public function updateHistoire() {
		$data = array("error"=>0, "msg"=>"");
		$mainframe = JFactory::getApplication();
		$session = JFactory::getSession();
		$user = JFactory::getUser();
		$jinput = $mainframe->input;
		
		$edit_orga = $user->authorise('core.edit_orga', 'com_perso_norlande');
		
		if($data["error"] === 0
			&& !$edit_orga)
		{
			$validation_user = $perso->userHasValidate();
			if($validation_user == true)
			{
				$data["error"] = 1;
				$data["msg"] = "Vous avez déjà validé votre personnage, seul un orga peut encore le modifier";
			}
		}
		
		
		if($data["error"] == 0) {
			$persoId = $session->get( 'perso_id', -1 );
			
			if($persoId == -1) {
				$data["msg"] = "Personnage non trouvé dans la session";
				$data["error"] = 1;
			}
		}
		
		if($data["error"] == 0) {
			$histoire = $jinput->get('histoire', '', 'STR');
			PersoHelper::updateHistoire($persoId, $histoire);
		}
		
		$mainframe->redirect('index.php?option=com_perso_norlande&view=detailsperso');
	}
	
	public function updateReliquat() {
		$data = array("error"=>0, "msg"=>"");
		$mainframe = JFactory::getApplication();
		$session = JFactory::getSession();
		$user = JFactory::getUser();
		$jinput = $mainframe->input;
		
		$edit_orga = $user->authorise('core.edit_orga', 'com_perso_norlande');
		
		if(!$edit_orga)
		{
			$data["error"] = 1;
			$data["msg"] = "Vous n'avez pas les droits nécessaire pour modifier cette donnée.";
		}
		
		
		if($data["error"] == 0) {
			$persoId = $session->get( 'perso_id', -1 );
			
			if($persoId == -1) {
				$data["msg"] = "Personnage non trouvé dans la session";
				$data["error"] = 1;
			}
		}
		
		if($data["error"] == 0) {
			$reliquat = $jinput->get('reliquat', '', 'STR');
			PersoHelper::updateReliquat($persoId, $reliquat);
		} else {
			error_log($data["msg"]);
		}
		
		$mainframe->redirect('index.php?option=com_perso_norlande&view=detailsperso');
	}
	
	public function validationUser() {
		$data = array("error"=>0, "msg"=>"");
		$mainframe = JFactory::getApplication();
		$session = JFactory::getSession();
		$user = JFactory::getUser();
		
		
		if($data["error"] == 0) {
			$persoId = $session->get( 'perso_id', -1 );
			
			if($persoId == -1) {
				$data["msg"] = "Personnage non trouvé dans la session";
				$data["error"] = 1;
			}
		}
		
		if($data["error"] == 0) {
			$perso = $this->getCurrentPerso();
			$lignee = $perso->getLignee();
			PersoHelper::validationUser($persoId);
			$mailOrga = Lignees::getOrgaMail($lignee);
			
			$subject = "Validation d'un personnage de votre lignée";
			$nomPerso = $perso->getNom();
			$msg = "Le personnage \"$nomPerso\" de la lignée \"$lignee\" a été validé.";
			$this->sendEmail($mailOrga, $subject, $msg);
		}
		
		$mainframe->redirect('index.php?option=com_perso_norlande&view=detailsperso');
	}
	
	public function associatePersoUser() {
		$session = JFactory::getSession();
		$data = array('error'=>0, 'msg'=>'Erreur inconnue');
		$mainframe = JFactory::getApplication();
		$jinput = $mainframe->input;
		
		$persoId = $session->get( 'perso_id', -1 );
		$userId = $this->getInt('user_id', -1);
		
		if($userId == -1) {
			$data['error'] = 1;
			$data['msg'] = 'Paramètre user_id invalide';
		}
		
		if($data['error'] == 0 
			&& $persoId == -1) {
			JLog::add(JText::_("Aucun personnage sélectionné actuellement"), JLog::ERROR, 'jerror');
			$data['error'] = 1;
			$data['msg'] = 'Aucun personnage sélectionné actuellement';
		}
		
		if($data['error'] == 0 ) {
			PersoHelper::associateUserPerso($persoId, $userId);
		}
		
		if($data['error'] == 0 ) {
			$data['msg'] = 'Association effectuée';
		}
		$mainframe->redirect('index.php?option=com_perso_norlande&view=detailsperso');
	}
	
	private function setCurrentPerso($perso_id) {
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
		$id = $session->get( 'perso_id', -1 );
		
		if($id == -1) {
			JLog::add(JText::_("Aucun personnage sélectionné actuellement"), JLog::ERROR, 'jerror');
		} else {
			$perso = PersoHelper::getPersoById($id);
		}
		return $perso;
	}
	
	public function selectPerso() {
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser();
		$edit_orga = $user->authorise('core.edit_orga', 'com_perso_norlande');
		
		if($edit_orga) {
			$jinput = JFactory::getApplication()->input;
	 		$perso_id = $jinput->get('perso_id', '0', 'STR');
	 		
	 		$this->setCurrentPerso($perso_id);
	 	}
		
		$mainframe->redirect('index.php?option=com_perso_norlande&view=detailsperso');
	}
	
	public function addEntrainement() {
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser();
		$edit_orga = $user->authorise('core.edit_orga', 'com_perso_norlande');
		
		if($edit_orga) {
			$model = null;
			$model = $this->getModel('detailsperso');		
			
			$jinput = $mainframe->input;
			//recherche des résultats dans la base de données
	
			$competence_id = $jinput->get('competence_id', '0', 'STR');
			$perso = $this->getCurrentPerso();
	
			$result = $model->addEntrainement($perso, $competence_id);
			echo json_encode($result);
		}
		$mainframe->close();
	}
	
	
	// Méthode appelée lorsqu'un orga supprime un entrainement
	// de la liste des entrainements acquis d'un perso
	// (AJAX)
	public function deleteEntrainement() {
		$model = $this->getModel('detailsperso');	
		$mainframe = JFactory::getApplication();		
		$user = JFactory::getUser();
		$edit_orga = $user->authorise('core.edit_orga', 'com_perso_norlande');
		
		if($edit_orga) {
			$jinput = $mainframe->input;
			//recherche des résultats dans la base de données
	
			$competence_id = $jinput->get('competence_id', '0', 'STR');			
			$perso = $this->getCurrentPerso();
			 
			echo json_encode($model->deleteEntrainement($perso, $competence_id));
		}
		$mainframe->close();
	}	
	
	public function askForPersoAssociation() {
		$data = array("error"=>0, "msg"=>"Message envoyé.");
		$mainframe = JFactory::getApplication();		
		$user = JFactory::getUser();
		$jinput = $mainframe->input;
		
		$lignee_id = $jinput->get('lignee_partie_precedente', -1, 'INT');
		if(array_key_exists($lignee_id, Lignees::$lignees)) {
			$lignee = Lignees::$lignees[$lignee_id];
		} else {
			$data["error"] = -1;
			$data["msg"] = "Lignée inconnue";
		}
		
		if($data["error"] == 0) {
			$msg = $jinput->get('demande_assoc_user_perso','', 'STR');
			if($msg == '') {
				$data["error"] = -1;
				$data["msg"] = "Le message envoyé ne doit pas être vide";
			}
		}
		
		if($data["error"] == 0) {
			if($msg == '') {
				$data["error"] = -1;
				$data["msg"] = "Lignée inconnue";
			}
		}
		
		if($data["error"] == 0) {
			if (strpos($msg, 'NOM_PERSONNAGE') !== false) {
			   $data["error"] = -1;
				$data["msg"] = "Vous devez indiquer le nom de votre personnage";
			}
			if (strpos($msg, 'LIGNEE_PRECEDENTE') !== false) {
				$data["error"] = -1;
				$data["msg"] = "Vous devez indiquer la lignée dans laquelle était votre personnage";
			}
			$username = $user->name;
			$msg = $msg . "\nLe personnage devra être associé à '$username'.";
		}
		
		if($data["error"] == 0) {
			$mailOrga = Lignees::getOrgaMail($lignee);
			$subject = "Demande d'association personnage/utilisateur";
			
			$this->sendEmail($mailOrga, $subject, $msg);
		}
		echo json_encode($data);
		$mainframe->close();
	}	
	
	public function createPerso() {
		$error = 0;
		$msg_error = "";
		
		$mainframe = JFactory::getApplication();		
		$jinput = $mainframe->input;
		$user = JFactory::getUser();
		$edit_orga = $user->authorise('core.edit_orga', 'com_perso_norlande');
		
		if(PersoHelper::getPersoIdFromUser($user->id) != -1
			&& !$edit_orga) {
			$error = 1;
			$msg_error = "Un utilisateur ne peut pas créer plus d'un personnage";
		}
 
		//recherche des résultats dans la base de données

		if($error === 0) {
			$lignee_id = $jinput->get('lignee_perso', -1, 'INT');
			if(array_key_exists($lignee_id, Lignees::$lignees)) {
				$lignee = Lignees::$lignees[$lignee_id];
			} else {
				$error = 2;
				$msg_error = "Lignée inconnue";
			}
		}
		
		if($error === 0) {
			$nom = $jinput->get('nom_perso', "", 'STR');
			if($nom === "") {
				$error = 3;
				$msg_error = "Il faut renseigner le nom du personnage";
			}
		}
		
		if($error === 0) {
			try {
				$newId = PersoHelper::insertPerso($nom, $lignee);
				$this->setCurrentPerso($newId);
				if(!$edit_orga) {
					PersoHelper::associateUserPerso($newId, $user->id);
				}
			} catch(Exception $e) {
				$error = 4;
				$msg_error = "Erreur lors de l'insertion d'un perso en BDD";
				error_log("Erreur lors de l'insertion d'un perso en BDD : ".$e);
			}
		}

		$mainframe->redirect('index.php?option=com_perso_norlande&view=detailsperso');
	}	
	
	
	private function sendEmail($dest, $subject, $msg) {
		$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();
		$sender = array(
			$config->get('mailfrom'),
			$config->get('fromname')
		);
		
		$mailer->setSender($sender);
		$mailer->addRecipient($dest);
		$mailer->setBody($msg);
		$mailer->setSubject($subject);
		$sent = $mailer->Send();
		if($sent !== true) {
			error_log("Error when sending email to ".$dest);
		}
	}
}

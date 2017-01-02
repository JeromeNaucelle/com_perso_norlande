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

		 
		echo json_encode($model->addEntrainement($perso, $competence_id));
		$mainframe->close();
	}
		
}

<?php
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
jimport('joomla.log.log');

//require(JPATH_ROOT."/components/com_perso_norlande/includes/Perso.inc");
require_once JPATH_COMPONENT . '/includes/Perso.php';
require_once JPATH_COMPONENT . '/includes/Arbre.php';
require_once JPATH_COMPONENT . '/helpers/PersoHelper.php';
 
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
			//error_log("ajout de l'entraineur ".$maitrise['competence_nom']);
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
		//error_log("getArbreMaitrise 1");
		$return = array();
		
		try {
			$db = JFactory::getDbo();
	 
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('a.competence_id','a.competence_nom', 'a.parent_id')));
			$query->from($db->quoteName('competences', 'a'));
			$query->join('INNER', $db->quoteName('competences', 'b') . ' ON (' . $db->quoteName('a.maitrise') . ' = ' . $db->quoteName('b.competence_nom') . ')');
			$query->where($db->quoteName('b.competence_id') . ' = ' . $competence_id);
			
			//error_log("getArbreMaitrise 3 : $query");
			 
			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			$results = $db->loadAssocList();
			//error_log("getArbreMaitrise 4");
			
			
			for($i=0; $i<count($results); $i++)
			{
				$parent = '';
				$maitrise = $results[$i];
				if($maitrise['parent_id'] != 0){
					$parent = $maitrise['parent_id'];
				}
				$return[] = array(array('v' => $maitrise['competence_id'],'f'=>'<h1>'.htmlentities($maitrise['competence_nom']).'</h1>'), $parent);
			}
		} catch(Exception $e) {
			error_log("getArbreMaitrise exception : $e");
		}
		
		return $return;
	}
	
	
	public function getArbreMaitrisePhp($competence_id)
	{
		$return = array();
		$arbre = null;
		
		$db = JFactory::getDbo();
 
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from($db->quoteName('competences', 'a'));
		$query->join('INNER', $db->quoteName('competences', 'b') . ' ON (' . $db->quoteName('a.maitrise') . ' = ' . $db->quoteName('b.maitrise') . ')');
		$query->where($db->quoteName('b.competence_id') . ' = ' . $competence_id);
		 
		// Reset the query using our newly populated query object.
		
		try {
		$db->setQuery($query);
		$results = $db->loadAssocList();
		$arbre = new ArbreMaitrise($results);
		} catch(Exception $e) {
			error_log("getArbreMaitrisePhp catch $e");
		}
		
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

<?php

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/includes/Competence.php';

class Perso {

	private $id;
	private $nom = "nom base";
	private $lignee;
	private $competences;

    function __construct() {
    	JLog::add(JText::_('In BaseClass constructor'), JLog::WARNING, 'jerror');
    	$this->nom = "nom base";
    	$competences = array();
    }
    
    private function checkCompetencesRequises($arbre, $competence_id)
    {
    	$competencesRequises = array();
    	
    	if($arbre->getCompetence($competence_id)->getParentId() === 0)
    	{
    		// C'est une compétence racine, pas de pré-requis
    		return $competencesRequises;
    	}
    	$path = $arbre->getPathForCompetence($competence_id);
    	foreach($path as $competenceRequise_id)
    	{
    		if( !isset($this->competences[$competenceRequise_id]) )
    		{
    			array_push($competencesRequises, $competenceRequise_id);
    		}
    	}
    	return $competencesRequises;
    }
    
    	
    public function can_develop($competence_id, $arbre)
    {
    	$result = ""; 
    	
    	error_log("can_develop(". $competence_id .")");
    	$competenceFromMaitrise = array();
    	foreach($this->competences as $key => $competence) {
    		if($competence->getMaitrise() === $arbre->getMaitrise())
    		{
    			error_log("can_develop 1 : key = ".$key);
    			$competenceFromMaitrise[$key] = $competence;
    		}
    	}
    	error_log("can_develop 2");
    	if(count($competenceFromMaitrise) == 0)
    	{
    		error_log("can_develop 3");
    		//aucune competence actuellement dans cette maitrise
    		$result = $this->checkCompetencesRequises($arbre, $competence_id);
    		return $result;
    	}
    	
    	// TODO : boucler au cas où plusieurs compétences entraineurs
    	error_log("can_develop 4");
    	foreach($competenceFromMaitrise as $key => $competence) {
    		if( $competence->isEntraineur() )
    		{
    			$path = $arbre->getPathForCompetence($key);
    			break;
    		}
    	}
    	error_log("can_develop 5");
    	$path = $arbre->getPathForCompetence($competence_id);
    	error_log("can_develop 5. Path = ".json_encode($path));
    	error_log("can_develop 5. competenceFromMaitrise = ".json_encode($competenceFromMaitrise));
    	foreach($path as $key)
    	{
    		unset($competenceFromMaitrise[$key]);
    	}
    	error_log("can_develop 5. competenceFromMaitrise = ".json_encode($competenceFromMaitrise));
    	if(count($competenceFromMaitrise) === 0)
    	{
    		error_log("can_develop 6");
    		//pas de contre_indication à l'aprentissage
    		//vérification des pré requis
    		$result = $this->checkCompetencesRequises($arbre, $competence_id);
    		if(count($result) === 0)
    		{
    			error_log("Tous les prérequis sont remplis");
    		} else {
    			error_log("Les competences pre-requises sont : " . json_encode($result));
			}    	
    	}
    	else {
    		error_log("can_develop 7");
    		$result = "Une autre branche de cette maitrise est en cours d'apprentissage, ";
    		$result = $result . "vous ne pouvez pas apprendre cette compétence";
    		error_log($result);
    	}
		// fin TODO    	
		return $result;
    	
    }
    
    
	public static function create($query_result, $competences_result)
	{
		$perso = new Perso();
		$perso->nom = $query_result['nom'];
		$perso->lignee = $query_result['lignee'];
		for($i=0; $i<count($competences_result); $i++)
		{
			$competence = Competence::create($competences_result[$i]);
			$perso->competences[$competence->getId()] = $competence;
		}
		//TODO
		//$perso->competences = $query_result['maitrises'];
		return $perso;
	}

  public function getNom(){
		return $this->nom;
  }
}
?>
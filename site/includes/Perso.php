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
    	$result = array("result" => 0, "msg" =>"", "competences" => array());
    	if(isset($this->competences[$competence_id])) {
    		
    		//Compétences déjà acquise
    		return $result;
    	}
    	
    	error_log("can_develop(". $competence_id .")");
    	// On récupère toutes les compétences acquises dans cette maitrise
    	$competenceFromMaitrise = array();
    	foreach($this->competences as $key => $competence) {
    		if($competence->getMaitrise() === $arbre->getMaitrise())
    		{
    			$competenceFromMaitrise[$key] = $competence;
    		}
    	}
    	if(count($competenceFromMaitrise) == 0)
    	{
    		//aucune competence actuellement dans cette maitrise
    		array_push($result['competences'], $competence_id);
    		return $result;
    	}
    	
    	$path = array();
    	foreach($competenceFromMaitrise as $key => $competence) {
    		error_log("testing competence ".$competence->getNom());
    		if( $competence->isEntraineur() )
    		{
    			error_log("C'est un entraineur key = ".$key);
    			array_push($path, $key);
    			$path = array_merge($path, $arbre->getPathForCompetence($key));
    		}
    	}
    	
    	// on supprime de la liste les compétences appartenant à une branche complétée (entraineur atteint)
    	foreach($path as $key)
    	{
    		unset($competenceFromMaitrise[$key]);
    	}
    	
    	$pre_requis = $arbre->getPathForCompetence($competence_id);
    	
		// On vérifie si les compétences restantes font parties des pré-requis
		foreach($pre_requis as $key)
    	{
    		unset($competenceFromMaitrise[$key]);
    	}
    	
    	if(count($competenceFromMaitrise) === 0)
    	{
    		//pas de contre_indication à l'aprentissage
    		//On retire de la liste des pré-requis les compétences déjà acquises
    		$to_learn =  array_diff($pre_requis , array_keys($this->competences) );
    		if(count($to_learn) === 0)
    		{
    			error_log("Tous les prérequis sont remplis");
    			array_push($result['competences'], $competence_id);
    		} else {
    			$result['competences'] = $to_learn;
    			array_push($result['competences'], $competence_id);
    			error_log("Les competences pre-requises sont : " . json_encode($to_learn));
			}    	
    	}
    	else {
    		$msg = "Une autre branche de cette maitrise est en cours d'apprentissage, ";
    		$msg = $msg . "vous ne pouvez pas apprendre cette compétence";
    		$result['result'] = 1;
    		$result['msg'] = $msg;
    		error_log($msg);
    	}  	
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
  
  public function getCompetences()
  {
  		return $this->competences;
  }
}
?>
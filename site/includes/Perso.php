<?php

defined('_JEXEC') or die;

class Perso {

	private $id;
	private $nom = "nom base";
	private $lignee;
	private $competences;

    function __construct() {
    	JLog::add(JText::_('In BaseClass constructor'), JLog::WARNING, 'jerror');
    	$this->nom = "nom base";
    }
    
    private function setCompetences($competences)
    {
    	
    }
    	
    public function can_develop($competence)
    {
    	
    }
    
    
	public static function create($query_result)
	{
		$perso = new Perso();
		$perso->nom = $query_result['nom'];
		$perso->lignee = $query_result['lignee'];
		//TODO
		//$perso->competences = $query_result['maitrises'];
		return $perso;
	}

  public function getNom(){
		return $this->nom;
  }
}
?>
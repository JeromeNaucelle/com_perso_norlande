<?php

defined('_JEXEC') or die;

class Competence {

	private $id;
	private $nom;
	private $maitrise;
	private $parent_id;
	private $famille;
	private $niveau;
	//boolean
	private $entraineur;

   function __construct() {
   	JLog::add(JText::_('In BaseClass constructor'), JLog::WARNING, 'jerror');
   	$this->nom = "nom base";
   	$this->niveau = 1;
   	$this->parent_id = 0;
   	$this->entraineur = false;
   }
   
   public static function create($query_result)
   {
   	$competence = new Competence();
		$competence->nom = $query_result['competence_nom'];
   	$competence->id = (int)$query_result['competence_id'];
   	$competence->famille = $query_result['famille'];
   	$competence->maitrise = $query_result['maitrise'];
   	$competence->parent_id = (int)$query_result['parent_id'];
   	$competence->niveau = (int)$query_result['niveau'];
   	$competence->entraineur = $query_result['entraineur'];
   	return $competence;
   }

	public function getNom(){
		return $this->nom;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getMaitrise(){
		return $this->maitrise;
	}
	
	public function getParentId(){
		return $this->parent_id;
	}
	
	public function getFamille() {
		return $this->famille;
	}
	
	public function getNiveau(){
		return $this->niveau;
	}
	
	public function isEntraineur(){
		return $this->entraineur;
	}
	
	public function __toString() 
	{
		$str = $this->nom;
		return $str;
	}
}
?>
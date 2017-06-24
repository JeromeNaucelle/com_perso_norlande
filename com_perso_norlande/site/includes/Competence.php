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
	// 1 si l'orga a déjà validé la fiche ou
	// a lui même ajouté cette compétence
	private $valide;

   function __construct() {
   	$this->nom = "nom base";
   	$this->niveau = 1;
   	$this->parent_id = 0;
   	$this->entraineur = false;
   	$this->valide = false;
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
   	
   	// le champs "valide" n'existe que lorsqu'une compétence
   	// est associé à un personnage. (il vien de la table `perso_user`
   	if( isset($query_result['valide']) ) {
   		$competence->valide = $query_result['valide'];
   	}
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
	
	public function isAlredayValidated(){
		return $this->valide;
	}
	
	public function __toString() 
	{
		$str = $this->nom;
		return $str;
	}
}


class CompetenceFamille {
	private $famille;
	private $compByLevel;
	
	function __construct() {
		$this->compByLevel = array(1 => array(),2 => array(),3 => array(), 4 => array(), 5 => array());
	}
	
	public function setFamille($famille) {
		$this->famille = $famille;
	}
	
	public function getFamille() {
		return $this->famille;
	}
	
	public function addCompetence($competence) {
		$niveau = $competence->getNiveau();
		array_push($this->compByLevel[$niveau], $competence);
	}
	
	public function getFromLevel($niveau) {
		return join('<br>', $this->compByLevel[$niveau]);
	}
	
	public function getNiveauMax() {
		for($i = 5; $i >= 0; $i -= 1) {
			if(count($this->compByLevel[$i]) > 0) {
				return $i;
			}
		}
	}
}
?>
<?php

defined('_JEXEC') or die;

class Competence {

	private $id;
	private $nom;
	private $nom_id;
	private $maitrise;
	private $competence_requise;
	private $famille;
	private $niveau;

   function __construct() {
   	JLog::add(JText::_('In BaseClass constructor'), JLog::WARNING, 'jerror');
   	$this->nom = "nom base";
   	$this->niveau = 1;
   	$this->competence_requise = "";
   }
   
   public static function create($query_result)
   {
   	$competence = new Competence();
		$competence->nom = $query_result['nom_brut'];
   	$competence->nom_id = $query_result['nom_format'];
   	$competence->famille = $query_result['famille'];
   	$competence->maitrise = $query_result['maitrise'];
   	$competence->requis = $query_result['parent'];
   	$competence->niveau = $query_result['niveau'];
   	return competence;
   }

	function getNom(){
		return $this->nom;
	}
}
?>
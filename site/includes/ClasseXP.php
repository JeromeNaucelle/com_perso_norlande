<?php

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/includes/define.php';

//TODO : faire des vérification sur le type de cristal
class ClasseXP {
	private $cristaux;
	
	// array sous la forme '$competence_id' => 'nom_comptence'
	private $entrainements;
	
	function __construct()
	{
		$this->cristaux = array();
		foreach(ClasseXP::getTypesCristaux() as $type) {
			$this->cristaux[$type] = 0;
		}
		
		$this->entrainements = array();
	}
	
	public static function getTypesCristaux() {
		$res = array(OCCULTISME, SOCIETE, INTRIGUE, BELLIGERANCE, INCOLORE);
		return $res;
	}
	
	public function setCristaux($famille, $val) {
		$this->cristaux[$famille] = $val;
	}
	
	public function getCristaux($famille) {
		return $this->cristaux[$famille];
	}
	
	public function addEntrainement($id_competence, $nom_competence) {
		$this->entrainements[$id_competence] = $nom_competence;
	}
	
	public function getEntrainements() {
		return $this->entrainements;
	}
	
	public function toArray() {
		$result["cristaux"] = $this->cristaux;
    	$result["entrainements"] = $this->entrainements;
    	return $result;
	}
	
}


?>
<?php

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/includes/define.php';

//TODO : faire des vérification sur le type de cristal
class ClasseXP {
	private $cristaux;
	private $entrainements;
	
	function __construct()
	{
		$this->cristaux = array();
		foreach(ClasseXP::get_types_cristaux() as $type) {
			$this->cristaux[$type] = 0;
		}
		
		$this->entrainements = array();
	}
	
	public static function get_types_cristaux() {
		$res = array(OCCULTISME, SOCIETE, INTRIGUE, BELLIGERANCE, INCOLORE);
		return $res;
	}
	
	public function set_cristaux($famille, $val) {
		$this->cristaux[$famille] = $val;
	}
	
	public function get_cristaux($famille) {
		return $this->cristaux[$famille];
	}
	
	public function add_entrainement($id_competence, $nom_competence) {
		$this->entrainements[$id_competence] = $nom_competence;
	}
	
	public function get_entrainements() {
		return $this->entrainements;
	}
	
	public function to_array() {
		$result["cristaux"] = $this->cristaux;
    	$result["entrainements"] = $this->entrainements;
    	return $result;
	}
	
}


?>
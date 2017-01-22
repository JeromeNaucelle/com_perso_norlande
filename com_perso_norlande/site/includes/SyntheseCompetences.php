<?php

defined('_JEXEC') or die;

class SyntheseCompetences {

	private $persoId;
	private $nom;
	private $competences;
	
	private $lecture_ecriture;
	private $rumeurs;
	private $actions_guerre;
	private $coup_force;
	private $voix_noires;
	private $voix_blanches;
	private $voix_peuple;
	private $voix_roi;
	private $veto;
	private $manigance;
	private $lieux_pouvoir;
	private $force_physique;
	private $bonus_mana;
	private $globes_sortilege;
	private $bonus_coups;
	private $bonus_coup_etoffe;
	private $esquive_etoffe;
	private $resiste_etoffe;
	private $esquive_cuir;
	private $resiste_cuir;
	private $esquive_maille;
	private $resiste_maille;
	private $esquive_plaque;
	private $resiste_plaque;

   function __construct() {
   	$this->parent_id = 0;
   	$this->lecture_ecriture = 0;
		$this->rumeurs = 0;
		$this->actions_guerre = 0;
		$this->coup_force = 0;
		$this->voix_noires = 0;
		$this->voix_blanches = 0;
		$this->voix_peuple = 0;
		$this->voix_roi = 0;
		$this->veto = 0;
		$this->manigance = 0;
		$this->lieux_pouvoir = "Aucun";
		$this->force_physique = 0;
		$this->bonus_mana = 0;
		$this->globes_sortilege = 0;
		$this->bonus_coups = 0;
		$this->bonus_coup_etoffe = 0;
		$this->esquive_etoffe = 0;
		$this->resiste_etoffe = 0;
		$this->esquive_cuir = 0;
		$this->resiste_cuir = 0;
		$this->esquive_maille = 0;
		$this->resiste_maille = 0;
		$this->esquive_plaque = 0;
		$this->resiste_plaque = 0;
   }
   
   public static function create($persoId)
   {
   	$synthese = new SyntheseCompetences();
   	$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query_competences = $db->getQuery(true);
		$query_competences
			->select('a.*')
			->from($db->quoteName('competences', 'a'))
			->join('INNER', $db->quoteName('persos_competences', 'b') . ' ON (' . $db->quoteName('a.competence_id') . ' = ' . $db->quoteName('b.competence_id') . ')')
			->where($db->quoteName('b.id_perso') . ' = ' . $persoId);
			
		$db->setQuery($query_competences);
		$result_competences = $db->loadAssocList();
		
		foreach($result_competences as $array) {
			foreach($array as $key => $value) {
				if($key === "lieux_pouvoir") {
					// TODO grep pour check Guerre ou Conseil
				} else {
					error_log("key : ".$key);
					error_log("val : ".$value);
					$synthese->$key = $synthese->$key + $value;
				}
			}
		}
		return $synthese;
   }
	
	public function getActionsGuerre(){
		return $this->actions_guerre;
	}	
	
	public function getRumeurs(){
		return $this->rumeurs;
	}
	
}
?>
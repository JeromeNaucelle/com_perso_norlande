<?php

defined('_JEXEC') or die;

class DefinitionPiege {
	
	public $cout;
	public $effet;
	public $nom;
	
	public function __construct($value) {
    $arr = explode('|', $value);
    $this->nom = $arr[0];
    $this->cout = $arr[1];
    $this->effet = $arr[2];
  }
  
  public static function defaultValue() {
  		return new DefinitionPiege("-|-|-");
  	}
}

class_alias ('DefinitionPiege','DefinitionBreuvage');
class_alias ('DefinitionPiege','DefinitionTechnique');
class_alias ('DefinitionPiege','DefinitionInvocation');

class DefinitionPouvoir {
	
	public $effet;
	public $nom;
	
	public function __construct($value) {
    $arr = explode(' ; ', $value);
    $this->nom = $arr[0];
    $this->effet = $arr[1];
  }
  
  public static function defaultValue() {
  		return new DefinitionPouvoir("-|-");
  	}
}

class DefinitionCapacite {
	
	public $effet;
	public $frequence;
	
	public function __construct($value) {
    $arr = explode('|', $value);
    $this->effet = $arr[0];
    $this->frequence = $arr[1];
  }
  
  public static function defaultValue() {
  		return new DefinitionCapacite("-|-");
  	}
}

class SyntheseCompetences {

	private $persoId;
	private $nom;
	private $competences;
	
	/* Creation d'un tableau de la forme
		"colonne BDD" => "Label" */
	private static $corres_label = array(
	"parcelles" => "Possessions de départs (parcelles)",
	"possessions_depart" => "Possessions de départs",
	"a_prevoir" => "Objet de jeu que vous devez prévoir",
	"connaissances" => "Connaissances",
	"aide_jeu" => "Aides de jeu",
	
	"lecture_ecriture" => "Lecture et écriture",
	"rumeurs" => "Rumeurs",
	"actions_guerre" => "Actions de Guerre",
	"coup_force" => "Coup de force",
	"voix_noires" => "Voix noires",
	"voix_blanches" => "Voix blanches",
	"voix_peuple" => "Voix du peuple",
	"voix_roi" => "Voix du roi",
	"veto" => "Veto",
	"manigance" => "Manigances",
	"lieux_pouvoir" => "Lieux de pouvoir",
	"force_physique" => "Force physique",
	"bonus_mana" => "Bonus de mana",
	"globes_sortilege" => "Globes de sortilège",
	"bonus_coups" => "Bonus coups",
	"bonus_coups_etoffe" => "Bonus coups (étoffe)",
	"esquive_etoffe" => "Esquive (étoffe)",
	"resiste_etoffe" => "Résiste (étoffe)",
	"esquive_cuir" => "Esquive (cuir)",
	"resiste_cuir" => "Résiste (cuir)",
	"esquive_maille" => "Esquive (maille)",
	"resiste_maille" => "Résiste (maille)",
	"esquive_plaque" => "Esquive (plaque)",
	"resiste_plaque" => "Résiste (plaque)",
	"maniement" => "Maniement",
	"attaque_spe" => "Attaque spéciale",
	"attaque_spe_tranchant" => "Attaque tranchante",
	"attaque_spe_contondant" => "Attaque contondante",
	"attaque_spe_hast" => "Attaque d'hast",
	"attaque_spe_tir" => "Attaque de tir",
	"attaque_spe_lancer" => "Attaque de lancer",
	"sortilege" => "Sortilège",
	"sort_masse1" => "Sort de masse",
	"sort_masse2" => "Sort de masse",
	"immunite_etoffe" => "Immunité (étoffe)",
	"immunite_cuir" => "Immunité (cuir)",
	"immunite_plaque" => "Immunité (plaque)",
	"immunite_maille" => "Immunité (maille)",
	"amelioration" => "Amélioration",
	"capacite" => "Capacité", // fusion de capacité et fréquence
	"technique1" => "Technique", // fusion technique 1(nom), technique 1(cout), technique 1(effet)
	"technique2" => "Technique", // fusion technique 2(nom), technique 2(cout), technique 2(effet)
	"piege1" => "Piège", // fusion Piège 1(nom, type, niveau), Piège 1 (coût), Piège 1 (effet)
	"piege2" => "Piège", // fusion Piège 2(nom, type, niveau), Piège 2 (coût), Piège 2 (effet)
	"breuvage1" => "Breuvage", // fusion breuvage 1(nom, type, niveau), breuvage 1 (coût), breuvage 1 (effet)
	"breuvage2" => "Breuvage", // fusion breuvage 2(nom, type, niveau), breuvage 1 (coût), breuvage 1 (effet)
	"breuvage3" => "Breuvage", // fusion breuvage 3(nom, type, niveau), breuvage 1 (coût), breuvage 1 (effet)
	"breuvage4" => "Breuvage", // fusion breuvage 4(nom, type, niveau), breuvage 1 (coût), breuvage 1 (effet)
	"breuvage5" => "Breuvage", // fusion breuvage 5(nom, type, niveau), breuvage 1 (coût), breuvage 1 (effet)
	"invocation1" => "Invocation", // fusion invocation 1(type, niveau), invocation 1 (coût), invocation 1 (service)
	"invocation2" => "Invocation", // fusion invocation 2(type, niveau), invocation 2 (coût), invocation 2 (service)
	"metamorphose" => "Métamorphose",
	"pouvoir1" => "Pouvoir magique", // fusion Pouvoir magique 1(nom, type, mana), Formule et effet
	"pouvoir2" => "Pouvoir magique", // fusion Pouvoir magique 1(nom, type, mana), Formule et effet
	"pouvoir3" => "Pouvoir magique", // fusion Pouvoir magique 1(nom, type, mana), Formule et effet
	"pouvoir4" => "Pouvoir magique", // fusion Pouvoir magique 1(nom, type, mana), Formule et effet
	);

	
	
	
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
		$this->lieux_pouvoir = array();
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
		$this->pieges = array();
		$this->techniques = array();
		$this->breuvages = array();
		$this->invocations = array();
		$this->parcelles = array();
		$this->connaissances = array();
		$this->pouvoirs_magiques = array();
		$this->ameliorations = array();
		$this->capacites = array();
		$this->aide_jeu = array();
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
				if($key === "actions_guerre") {
					$synthese->actions_guerre += intval($value);
					
				} else if($key === "rumeurs") {
					$synthese->rumeurs += intval($value);
					
				} else if(SyntheseCompetences::$corres_label[$key] === "Piège"
					&& $value != "") {
					
					$tmp = new DefinitionPiege($value);
					array_push($synthese->pieges, $tmp);
				} else if(SyntheseCompetences::$corres_label[$key] === "Technique"
					&& $value != "") {
					
					$tmp = new DefinitionTechnique($value);
					array_push($synthese->techniques, $tmp);
				} else if(SyntheseCompetences::$corres_label[$key] === "Breuvage"
					&& $value != "") {
					
					$tmp = new DefinitionBreuvage($value);
					array_push($synthese->breuvages, $tmp);
				} else if(SyntheseCompetences::$corres_label[$key] === "Invocation"
					&& $value != "") {
					
					$tmp = new DefinitionInvocation($value);
					array_push($synthese->invocations, $tmp);
				} else if(SyntheseCompetences::$corres_label[$key] === "Pouvoir magique"
					&& $value != "") {
					
					$tmp = new DefinitionPouvoir($value);
					array_push($synthese->pouvoirs_magiques, $tmp);
				} else if($key === "capacite"
					&& $value != "") {
					
					$tmp = new DefinitionCapacite($value);
					array_push($synthese->capacites, $tmp);
				} else if($key === "parcelles"
					&& $value != "") {
					
					array_push($synthese->parcelles, $value);
				} else if($key === "connaissances"
					&& $value != "") {
					
					array_push($synthese->connaissances, $value);
				} else if($key === "amelioration"
					&& $value != "") {
					
					array_push($synthese->ameliorations, $value);
				} else if($key === "aide_jeu"
					&& $value != "") {
					
					array_push($synthese->aide_jeu, $value);
				} else if($key === "lieux_pouvoir"
					&& $value != "") {
					
					array_push($synthese->lieux_pouvoir, $value);
				} 
				
				else if(is_numeric($value)) {
					#error_log("key : ".$key);
					#error_log("val : ".$value);
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
	
	public function getPieges(){
		if(count($this->pieges) == 0) {
			return array(DefinitionPiege::defaultValue());
		}
		return $this->pieges;
	}
	
	public function getTechniques(){
		if(count($this->techniques) == 0) {
			return array(DefinitionTechnique::defaultValue());
		}
		return $this->techniques;
	}
	
	public function getBreuvages(){
		if(count($this->breuvages) == 0) {
			return array(DefinitionBreuvage::defaultValue());
		}
		return $this->breuvages;
	}
	
	public function getPouvoirsMagiques(){
		if(count($this->pouvoirs_magiques) == 0) {
			return array(DefinitionPouvoir::defaultValue());
		}
		return $this->pouvoirs_magiques;
	}
	
	public function getInvocations(){
		if(count($this->invocations) == 0) {
			return array(DefinitionInvocation::defaultValue());
		}
		return $this->invocations;
	}
	
	public function getParcelles(){
		if(count($this->parcelles) == 0) {
			return array('Aucune');
		}
		return $this->parcelles;
	}
	
	public function getMana(){
		return $this->bonus_mana;
	}
	
	public function getCoups(){
		return 3 + $this->bonus_coups;
	}
	
	public function getEsquive($armure){
		$key = "esquive_".strtolower($armure);
		return $this->$key;
	}
	
	public function getResiste($armure){
		$key = "resiste_".strtolower($armure);
		return $this->$key;
	}
	
	public function getImmunite(){
		return "-";
	}
	
	public function getForcePhysique(){
		return $this->force_physique;
	}
	
	public function getConnaissances(){
		if(count($this->connaissances) == 0) {
			return array('Aucune');
		}
		return $this->connaissances;
	}
	
	public function getCapacites(){
		if(count($this->capacites) == 0) {
			return array(DefinitionCapacite::defaultValue());
		}
		return $this->capacites;
	}
	
	public function getAmeliorations(){
		if(count($this->ameliorations) == 0) {
			return array('Aucune');
		}
		return $this->ameliorations;
	}
	
	public function getLieuxPouvoir(){
		if(count($this->lieux_pouvoir) == 0) {
			return 'Aucun';
		}
		return implode('<br>', $this->lieux_pouvoir);
	}
	
	public function getAideJeu(){
		if(count($this->aide_jeu) == 0) {
			return 'Aucune';
		}
		return implode('<br>', $this->aide_jeu);
	}
	
}
?>
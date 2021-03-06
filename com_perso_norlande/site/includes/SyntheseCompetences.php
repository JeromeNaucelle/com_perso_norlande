<?php

defined('_JEXEC') or die;
require_once JPATH_COMPONENT . '/includes/BonusLigneeChecker.php';

/*
* TODO : 
* - préciser la limite : parcelle ramenant de l'argent
*/



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

class DefinitionMetamorphose {
	
	public $cout;
	public $effet;
	
	public function __construct($value) {
    $arr = explode('|', $value);
    $this->cout = $arr[0];
    $this->effet = $arr[1];
  }
  
  public static function defaultValue() {
  		return new DefinitionMetamorphose("-|-");
  	}
}

class DefinitionSortilege {
	
	public $nom;
	public $formule;
	public $effet;
	
	public function __construct($value) {
    $arr = explode("|", $value);
    $this->nom = $arr[0];
    $this->formule = $arr[1];
    $this->effet = $arr[2];
  }
  
  public static function defaultValue() {
  		return new DefinitionSortilege("-|-|-");
  	}
}

class_alias ('DefinitionSortilege','DefinitionSortMasse');
class_alias ('DefinitionSortilege','DefinitionPouvoir');

class SyntheseLangues {
	
	public $secrets;
	public $echosForestiers;
	public $niveauLangue;
	
	public function __construct() {
    $this->secrets = 0;
    $this->echosForestiers = 0;
    $this->niveauLangue = 0;
  }
  
	public function checkValue($value) {
   	if(strpos($value, "forestiers") !== false) {
   		$this->echosForestiers += 1;
   		return;
   	}
   	if(strpos($value, "Secrets") !== false) {
   		$this->secrets += 1;
   		return;
   	}
   	$matches = array();
   	$ret = preg_match("/\+[0-9]+/", $value, $matches);
   	if($ret == 1) {
   		$tmp = substr($matches[0], 1);
   		$this->niveauLangue += intval($tmp);
   	}
   }
}

class SyntheseLieuxPouvoir{
	
	public $tableGuerre;
	public $tableConseil;
	
	public function __construct() {
    $this->tableGuerre = false;
    $this->tableConseil = false;
  }
  
  public function checkValue($value) {
   	$ret = preg_match("/guerre/i", $value);
   	if($ret == 1) {
   		$this->tableGuerre = true;
   	}
   	$ret = preg_match("/conseil/i", $value);
   	if($ret == 1) {
   		$this->tableConseil = true;
   	}
  }
}


class SyntheseAttaquesSpe{
	
	private $tranchant;
	private $contondant;
	private $hast;
	private $tir;
	private $lancer;
	private $toutType;
	
	public function __construct() {
    $this->tranchant = array();
    $this->contondant = array();
    $this->hast = array();
    $this->tir = array();
    $this->lancer = array();
    $this->toutType = array();
  }
  
  public function checkValue($type, $value) {
   	array_push($this->$type, $value);
  }
  
  public function getTypes() {
  		$types = array('tranchant'=>'Attaques tranchantes', 'contondant'=>'Attaques contondante', 
  			'hast'=>"Attaques d'hast et bâtons", 'tir'=>'Attaques de tir', 
  			'lancer'=>'Attaques de lancer', 'toutType'=>"Attaques spéciales");
  		return $types;
  }
  
  public function getAttaques($type){
  		return $this->$type;
  }
}

class SyntheseCompetences {
	private $nom;
	private $competences;
	
	/* Creation d'un tableau de la forme
		"colonne BDD" => "Label" */
	private static $corres_label = array(
	"competence_nom" => "",
	"parcelles" => "Possessions de départs (parcelles)",
	"possessions_depart" => "Possessions de départs",
	"a_prevoir" => "Objet de jeu que vous devez prévoir",
	"connaissances" => "Connaissances",
	"aide_jeu" => "Aides de jeu",
	"niveau_langue" => "Niveau de langue",
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
	"bonus_coup_etoffe" => "Bonus coups (étoffe)",
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
	private $entraineur;
	
	

   private function __construct($perso) {
   	$this->lignee_perso = $perso->getLignee();
   	$this->anciennete_perso = $perso->getAnciennete();
   	
   	$this->bonus_famille_checker = new BonusLigneeChecker();
   	$this->parent_id = 0;
   	$this->lecture_ecriture = false;
		$this->rumeurs = 0;
		$this->actions_guerre = 0;
		$this->coup_force = 0;
		$this->voix_noires = 0;
		$this->voix_blanches = 0;
		$this->voix_peuple = 0;
		$this->voix_roi = 0;
		$this->veto = 0;
		$this->manigance = 0;
		$this->lieux_pouvoir = new SyntheseLieuxPouvoir();
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
		$this->synthese_langue = new SyntheseLangues();
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
		$this->immunite_etoffe = array();
		$this->immunite_cuir = array();
		$this->immunite_maille = array();
		$this->immunite_plaque = array();
		$this->sortileges = array();
		$this->possessions_depart = array();
		$this->a_prevoir = array();
		$this->sorts_masse = array();
		$this->attaques_spe = new SyntheseAttaquesSpe();
		$this->maniements = array();
		$this->metamorphoses = array();
		$this->entraineur = array();
   }
   
   public static function create($perso, $competencesClassees)
   { 	
   	$synthese = new SyntheseCompetences($perso);
   	$synthese->competences_classees = $competencesClassees;
   	
   	$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query_competences = $db->getQuery(true);
		$query_competences
			->select('a.*')
			->from($db->quoteName('competences', 'a'))
			->join('INNER', $db->quoteName('persos_competences', 'b') . ' ON (' . $db->quoteName('a.competence_id') . ' = ' . $db->quoteName('b.competence_id') . ')')
			->where($db->quoteName('b.id_perso') . ' = ' . $perso->getId());
			
		$db->setQuery($query_competences);
		$result_competences = $db->loadAssocList();
		
		//SyntheseLieuxPouvoir::deleteUnusedColumns($result_competences);
		$unused = array("competence_id" => 0, "famille" => 0,  
		"maitrise" =>0, "niveau"=>0, "parent_id"=>0);
		
		foreach($result_competences as $array) {
			
			$array = array_diff_key($array, $unused);
   	
			foreach($array as $key => $value) {
				if($value == "") {
					continue;
				}	
				
				if($key === "actions_guerre") {
					$synthese->actions_guerre += intval($value);
					
				} else if($key	== "entraineur") {
					if($value == 1) {
						$comp = substr( $array['competence_nom'], 13);
						array_push($synthese->entraineur, $comp);
					}
					
				} else if(substr( $key, 0, 9 )	== "immunite_") {
					array_push($synthese->$key, $value);
					
				} else if(substr( $key, 0, 11 )	== "attaque_spe") {
					$tmp = explode("_", $key);
					$type = "toutType";
					if(count($tmp) > 2) {
						$type = $tmp[2];
					}
					$synthese->attaques_spe->checkValue($type, $value);
					
				} else if($key === "rumeurs") {
					$synthese->rumeurs += intval($value);
					
				} else if($key === "maniement") {
					array_push($synthese->maniements, $value);
					
				} else if($key === "possessions_depart") {
					array_push($synthese->possessions_depart, $value);
					
				} else if($key === "niveau_langue") {
					$synthese->synthese_langue->checkValue($value);
					
				} else if($key === "a_prevoir") {
					array_push($synthese->a_prevoir, $value);
					
				} else if($key === "sortilege") {
					array_push($synthese->sortileges, new DefinitionSortilege($value));
					
				} else if(SyntheseCompetences::$corres_label[$key] === "Piège") {
					
					$tmp = new DefinitionPiege($value);
					array_push($synthese->pieges, $tmp);
				} else if(SyntheseCompetences::$corres_label[$key] === "Technique") {
					
					$tmp = new DefinitionTechnique($value);
					array_push($synthese->techniques, $tmp);
				} else if(SyntheseCompetences::$corres_label[$key] === "Breuvage") {
					
					$tmp = new DefinitionBreuvage($value);
					array_push($synthese->breuvages, $tmp);
				} else if(SyntheseCompetences::$corres_label[$key] === "Invocation") {
					
					$tmp = new DefinitionInvocation($value);
					array_push($synthese->invocations, $tmp);
				} else if(SyntheseCompetences::$corres_label[$key] === "Pouvoir magique") {
					
					$tmp = new DefinitionPouvoir($value);
					array_push($synthese->pouvoirs_magiques, $tmp);
				} else if(SyntheseCompetences::$corres_label[$key] === "Sort de masse") {
					
					$tmp = new DefinitionSortMasse($value);
					array_push($synthese->sorts_masse, $tmp);
				} else if($key === "metamorphose") {
					
					$tmp = new DefinitionMetamorphose($value);
					array_push($synthese->metamorphoses, $tmp);
				} else if($key === "capacite") {
					
					$tmp = new DefinitionCapacite($value);
					array_push($synthese->capacites, $tmp);
				} else if($key === "parcelles") {
					$parcelle = $value." (compétence : $array[competence_nom])";
					array_push($synthese->parcelles, $parcelle);
				} else if($key === "connaissances") {
					
					array_push($synthese->connaissances, $value);
				} else if($key === "amelioration") {
					
					array_push($synthese->ameliorations, $value);
				} else if($key === "aide_jeu") {
					
					array_push($synthese->aide_jeu, $value);
				} else if($key === "lieux_pouvoir") {
					
					$synthese->lieux_pouvoir->checkValue($value);
				} 
				else if($key === "lecture_ecriture") {
					if( $value == 1) {
						$synthese->lecture_ecriture = true;
					}
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
   
   public function getLectureEcriture() {
   	return $this->lecture_ecriture;
   }
	
	public function getActionsGuerre(){
		return $this->actions_guerre;
	}	
	
	public function getRumeurs(){
		return $this->rumeurs;
	}
	
	private function getPieges(){
		if(count($this->pieges) == 0) {
			return array(DefinitionPiege::defaultValue());
		}
		return $this->pieges;
	}
	
	public function getPiegesEtTechniques() {
		$piegeEtTechniques = array_merge($this->pieges, $this->techniques);
		if(count($piegeEtTechniques) == 0) {
			return array(DefinitionPiege::defaultValue());
		}
		return $piegeEtTechniques;
	}
	
	private function getTechniques(){
		if(count($this->techniques) == 0) {
			return array(DefinitionTechnique::defaultValue());
		}
		return $this->techniques;
	}
	
	private function getBreuvages(){
		if(count($this->breuvages) == 0) {
			return array(DefinitionBreuvage::defaultValue());
		}
		return $this->breuvages;
	}
	
	public function getBreuvagesEtInvocations() {
		$breuvagesEtInvocations = array_merge($this->breuvages, $this->invocations);
		if(count($breuvagesEtInvocations) == 0) {
			return array(DefinitionBreuvage::defaultValue());
		}
		return $breuvagesEtInvocations;
	}
	
	public function getPouvoirsMagiques(){
		if(count($this->pouvoirs_magiques) == 0) {
			return array(DefinitionPouvoir::defaultValue());
		}
		return $this->pouvoirs_magiques;
	}
	
	private function getInvocations(){
		if(count($this->invocations) == 0) {
			return array(DefinitionInvocation::defaultValue());
		}
		return $this->invocations;
	}
	
	public function getMetamorphoses(){
		if(count($this->metamorphoses) == 0) {
			return array(DefinitionMetamorphose::defaultValue());
		}
		return $this->metamorphoses;
	}
	
	public function getSortileges(){
		if(count($this->sortileges) == 0) {
			return array(DefinitionSortilege::defaultValue());
		}
		return $this->sortileges;
	}
	
	public function getSortsMasse(){
		if(count($this->sorts_masse) == 0) {
			return array(DefinitionSortMasse::defaultValue());
		}
		return $this->sorts_masse;
	}
	
	public function getParcelles(){
		if(count($this->parcelles) == 0) {
			return array('Aucune');
		}
		return $this->parcelles;
	}
	
	public function getMana(){
		$bonusFamille = 0;
		$lignee = $this->lignee_perso;
		$bonusFamilleChecker = $this->bonus_famille_checker;
		
		if( $bonusFamilleChecker->hasBonusOccultisme($lignee) ) {
			$competencesClasses = $this->competences_classees[OCCULTISME];
			$bonusFamille = $competencesClasses->getNiveauMax();
		}
		return $this->bonus_mana + $bonusFamille;
	}
	
	public function getSyntheseLangue(){
		$ret = array();
		$bonusFamille = 0;
		$lignee = $this->lignee_perso;
		$bonusFamilleChecker = $this->bonus_famille_checker;
		
		if( $bonusFamilleChecker->hasBonusSociete($lignee) ) {
			$competencesClasses = $this->competences_classees[SOCIETE];
			$bonusFamille = $competencesClasses->getNiveauMax();
		}	
		
		if($this->synthese_langue->secrets > 0) {
			array_push($ret, "+".$this->synthese_langue->secrets." Secrets");
		}
		if($this->synthese_langue->echosForestiers > 0) {
			array_push($ret, "+".$this->synthese_langue->echosForestiers." Echos forestiers");
		}
		
		$niveauLangue = $bonusFamille + $this->synthese_langue->niveauLangue;
		
		$ret = array_pad($ret, count($ret)+$niveauLangue, "A REMPLIR PAR UN ORGA");
		if($this->lecture_ecriture) {
			$ret[] = "GLYPHE PERSONNELLE";
		}
		return $ret;
	}
	
	public function getNiveauLangue() {
		$bonusFamille = 0;
		$lignee = $this->lignee_perso;
		$bonusFamilleChecker = $this->bonus_famille_checker;
		
		if( $bonusFamilleChecker->hasBonusSociete($lignee) ) {
			$competencesClasses = $this->competences_classees[SOCIETE];
			$bonusFamille = $competencesClasses->getNiveauMax();
		}	
		return $this->synthese_langue->niveauLangue + $bonusFamille;
	}
	
	public function getCoups($armure){
		$bonusFamille = 0;
		$bonusCoupEtoffe = 0;
		$lignee = $this->lignee_perso;
		$bonusFamilleChecker = $this->bonus_famille_checker;
		
		if( $bonusFamilleChecker->hasBonusBelligerance($lignee) ) {
			$competencesClasses = $this->competences_classees[BELLIGERANCE];
			$bonusFamille = $competencesClasses->getNiveauMax();
		}	
		if(strtolower($armure) == "etoffe") {
			$bonusCoupEtoffe = $this->bonus_coup_etoffe;
		}
		
		return 3 + $this->bonus_coups + $bonusFamille + $bonusCoupEtoffe;
	}
	
	public function getEsquive($armure){
		$key = "esquive_".strtolower($armure);
		return $this->$key;
	}
	
	public function getResiste($armure){
		$key = "resiste_".strtolower($armure);
		return $this->$key;
	}
	
	public function getImmunite($armure, $separator='<br>'){
		$key = "immunite_".strtolower($armure);
		if(count($this->$key) == 0) {
			return "-";
		}
		return implode($separator, $this->$key);
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
	
	public function getLieuxPouvoir($separator='<br>'){
		$ret = array();
		
		if($this->lieux_pouvoir->tableGuerre) {
			array_push($ret, "Table de Guerre");
		}
		if($this->lieux_pouvoir->tableConseil) {
			array_push($ret, "Table du Conseil");
		}
		if(count($ret) == 0) {
			return 'Aucun';
		}
		return implode($separator, $ret);
	}
	
	public function getAideJeu(){
		if(count($this->aide_jeu) == 0) {
			return array('Aucune');
		}
		return $this->aide_jeu;
	}
	
	public function getObjetsAPrevoir(){
		if(count($this->a_prevoir) == 0) {
			return array('- Rien -');
		}
		return $this->a_prevoir;
	}
	
	public function getManiements() {
		return $this->maniements;
	}
	
	public function getAttaquesSpe() {
		return $this->attaques_spe;
	}
	
	public function getMonnaie() {
		$bonusFamille = 0;
		$lignee = $this->lignee_perso;
		$bonusFamilleChecker = $this->bonus_famille_checker;
		
		if( $bonusFamilleChecker->hasBonusIntrigue($lignee) ) {
			$competencesClasses = $this->competences_classees[INTRIGUE];
			// 2*niveauMax d'intrigue en pièces d'or
			$bonusFamille = 2*$competencesClasses->getNiveauMax();
		}
		$monnaie = new Monnaie();
		$monnaie->piecesOr = $bonusFamille;
		
		return $monnaie;
	}
	
	public function getPossessionsDepart(){
		$possessions = array();
		if($this->rumeurs > 0) {
			array_push($possessions, $this->rumeurs . " rumeurs");
		}
		if($this->actions_guerre > 0) {
			array_push($possessions, $this->actions_guerre . " actions de guerre");
		}
		if($this->coup_force > 0) {
			array_push($possessions, $this->coup_force . " coups de force");
		}
		if($this->voix_noires > 0) {
			array_push($possessions, $this->voix_noires . " voix noires");
		}
		if($this->voix_blanches > 0) {
			array_push($possessions, $this->voix_blanches . " voix blanches");
		}
		if($this->voix_peuple > 0) {
			array_push($possessions, $this->voix_peuple . " voix du peuple");
		}
		if($this->voix_roi > 0) {
			array_push($possessions, $this->voix_roi . " voix du roi");
		}
		if($this->veto > 0) {
			array_push($possessions, $this->veto . " veto");
		}
		if($this->manigance > 0) {
			array_push($possessions, $this->manigance . " manigances");
		}
		if($this->globes_sortilege > 0) {
			array_push($possessions, $this->globes_sortilege . " globes sortilèges");
		}
		
		$result = array_merge($this->possessions_depart, $possessions);
		return $result;
	}
	
	public function getEntraineur() {
		if(count($this->entraineur) == 0) {
			return array('N∕A');
		}
		return $this->entraineur;
	}
	
}
?>
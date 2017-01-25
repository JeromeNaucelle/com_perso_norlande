<?php

defined('_JEXEC') or die;

class SyntheseCompetences {

	private $persoId;
	private $nom;
	private $competences;
	
	/* Creation d'un tableau de la forme
		"colonne BDD" => "Label" */
	private static array(
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
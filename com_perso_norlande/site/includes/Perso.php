<?php

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/includes/Competence.php';
require_once JPATH_COMPONENT . '/includes/ClasseXP.php';
require_once JPATH_COMPONENT . '/includes/Monnaie.php';
require_once JPATH_COMPONENT . '/includes/define.php';

class Perso {

	private $id;
	private $nom;
	private $lignee;
	private $competences;
	private $armure;
	private $monnaie;
	private $anciennete;
	
	//
	private $xp;
	private $derniere_session;
	private $validation_user;

    function __construct() {
    	$this->nom = "nom base";
    	$this->competences = array();
    	$this->monnaie = new Monnaie();
    	$this->xp = new ClasseXP();
    	$this->validation_user = false;
    }
    
    private function checkCompetencesRequises($arbre, $competence_id)
    {
    	$competencesRequises = array();
    	
    	if($arbre->getCompetence($competence_id)->getParentId() === 0)
    	{
    		// C'est une compétence racine, pas de pré-requis
    		return $competencesRequises;
    	}
    	$path = $arbre->getPathForCompetence($competence_id);
    	foreach($path as $competenceRequise_id)
    	{
    		if( !isset($this->competences[$competenceRequise_id]) )
    		{
    			array_push($competencesRequises, $competenceRequise_id);
    		}
    	}
    	return $competencesRequises;
    }
    
    private function maxNouvelleCompetenceAtteint() {
    	if($this->anciennete == 0) {
    		return false;
    	}
    	
    	$ret = false;
    	foreach($this->competences as $competence) {
    		if( $competence->isAlredayValidated() ) {
    			$ret = true;
    			break;
    		}
    	}
    	return $ret;
    }
    
    /* Les différents cas :
    		- la compétence demandée peut être acquise (return 1)
    		- une autre branche est en cours d'apprentissage
    			dans cette maitise (return 4, msg)
    		- une ou plusieurs compétences doivent être acquises
    			avant la compétence demandée (return 2, pre-requis)
    		- compétence déjà acquise (return 3)
    		- le personnage n'est pas nouveau et a déjà acquis une nouvelle
    			compétence cette année (return 5, msg)
    */
    public function canLearn($competence_id, $arbre, $editOrga)
    {
    	$result = array("result" => 3, "msg" =>"", "competences" => array());
    	if(isset($this->competences[$competence_id])) {
    		
    		//Compétences déjà acquise
    		return $result;
    	}
    	
    	if( !$editOrga 
    			&& $this->maxNouvelleCompetenceAtteint()) {
    		$result['result'] = 5;
    		$result['msg'] = "Vous avez déjà développé une nouvelle compétence cette année.";
    		return $result;
    	}
    	
    	error_log("canLearn(". $competence_id .")");
    	// On récupère toutes les compétences acquises dans cette maitrise
    	$competenceFromMaitrise = array();
    	foreach($this->competences as $key => $competence) {
    		if($competence->getMaitrise() === $arbre->getMaitrise())
    		{
    			$competenceFromMaitrise[$key] = $competence;
    		}
    	}
    	if(count($competenceFromMaitrise) == 0)
    	{
    		//aucune competence actuellement dans cette maitrise
    		array_push($result['competences'], $competence_id);
    		$result['result'] = 1;
    		return $result;
    	}
    	
    	// On crée la liste des compétences dont le niveau "Entrainement"
    	// a été atteint
    	$path = array();
    	foreach($competenceFromMaitrise as $key => $competence) {
    		//error_log("testing competence ".$competence->getNom());
    		if( $competence->isEntraineur() )
    		{
    			//error_log("C'est un entraineur key = ".$key);
    			array_push($path, $key);
    			$path = array_merge($path, $arbre->getPathForCompetence($key));
    		}
    	}
    	
    	// on supprime de la liste les compétences appartenant à une branche complétée (entraineur atteint)
    	foreach($path as $key)
    	{
    		unset($competenceFromMaitrise[$key]);
    	}
    	
    	// On récupère la liste des compétences requise
    	// avant l'aprentissage de celle demandée
    	$pre_requis = $arbre->getPathForCompetence($competence_id);
    	
		// On vérifie si les compétences restantes (branche commencée
		// mais pas achevée) font parties des pré-requis
		foreach($pre_requis as $key)
    	{
    		unset($competenceFromMaitrise[$key]);
    	}
    	
    	if(count($competenceFromMaitrise) === 0)
    	{
    		//pas de contre_indication à l'aprentissage
    		//On retire de la liste des pré-requis les compétences déjà acquises
    		$to_learn =  array_diff($pre_requis , array_keys($this->competences) );
    		if(count($to_learn) === 0)
    		{
    			$result['result'] = 1;
    			error_log("Tous les prérequis sont remplis");
    			array_push($result['competences'], $competence_id);
    		} else {
    			// Il faut également apprendre les pré-requis suivant
    			$result['result'] = 2;
    			$result['competences'] = $to_learn;
    			$result['pre-requis'] = $to_learn;
    			array_push($result['competences'], $competence_id);
    			error_log("Les competences pre-requises sont : " . json_encode($to_learn));
			}    	
    	}
    	else {
    		$msg = "Une autre branche de cette maitrise est en cours d'apprentissage, ";
    		$msg = $msg . "vous ne pouvez pas apprendre cette compétence";
    		$result['result'] = 4;
    		$result['msg'] = $msg;
    		error_log($msg);
    	}  	
		return $result;
    }
    
    public function deleteCompetence($competenceId) {
    	unset($this->competences[$competenceId]);
    }
    
    
	public static function create($query_result, $competences_result)
	{
		$perso = new Perso();
		$perso->nom = $query_result['nom'];
		$perso->lignee = $query_result['lignee'];
		//$perso->derniere_session = $query_result['derniere_session'];
		$perso->id = $query_result['id'];
		$perso->xp->setPointsCreation($query_result['points_creation']);
		for($i=0; $i<count($competences_result); $i++)
		{
			$competence = Competence::create($competences_result[$i]);
			$perso->competences[$competence->getId()] = $competence;
		}
		
		foreach(ClasseXP::getTypesCristaux() as $famille) {
			$perso->xp->setCristaux($famille, $query_result['cristaux_'.$famille]);
		}
		
		$entrainements = json_decode($query_result['entrainements']);
		foreach($entrainements as $id => $nom_competence) {
			$perso->xp->addEntrainement($id, $nom_competence);
		}
		
		$perso->armure = $query_result['armure'];
		$perso->monnaie->piecesOr = $query_result['pieces_or'];
		$perso->monnaie->piecesArgent = $query_result['pieces_argent'];
		$perso->monnaie->piecesCuivre = $query_result['pieces_cuivre'];
		$perso->anciennete = $query_result['anciennete'];
		$perso->validation_user = $query_result['validation_user'];
		return $perso;
	}
	
	public function getId(){
		return $this->id;
  }
  
	public function getAnciennete(){
		return $this->anciennete;
  }

  public function getNom(){
		return $this->nom;
  }
  
  public function getLignee(){
		return $this->lignee;
  }
  
  public function getCompetences()
  {
  		return $this->competences;
  }
  
  public function isNewPerso()
  {
  		if($this->derniereSession === NULL) {
  			return true;
  		} 
  		return false;
  }
	
	public function getXp() {
		return $this->xp;
	}
	
	public function getArmure() {
		return $this->armure;	
	}
	
	public function getXpForCompetence($competence_id, $arbre, $niveau) {
		$res = array();
		$famille = strtolower($arbre->getFamilleMaitrise());
		$cristaux = array();
		$total_cristaux = 0;
		foreach(array($famille, INCOLORE) as $type) {
			$tmp = $this->xp->getCristaux($type);
			if($tmp > 0) {
				$cristaux[$type] = $tmp;
				$total_cristaux += $tmp;
			}
		}
		if(count($cristaux) > 0 && $total_cristaux >= $niveau) {
			$res['cristaux'] = $cristaux;
		}
		
		$entrainement = array();
		foreach($this->xp->getEntrainements() as $id => $nom_competence) {
			if($arbre->isEntrainementFor($id, $competence_id)) {
				$entrainement[$id] = $nom_competence;
			}
		}
		if(count($entrainement) > 0) {
			$res['entrainement'] = $entrainement;
		}
		
		$pc = $this->xp->getPointsCreation();
		if($pc >= $niveau) {
			$res['points_creation'] = $pc;
		}
		return $res;
	}
	
	public function canForgetCompetence($competenceId) {
		foreach($this->competences as $competence) {
			if($competence->getParentId() == $competenceId) {
				return false;
			}
		}
		return true;
	}
	
	public function isValidatedCompetence($competenceId) {
		return $this->competences[$competenceId]->isAlredayValidated();
	}
	
	public function userHasValidate() {
		return $this->validation_user;
	}
	
	public function getMonnaie() {
		return $this->monnaie;
	}	
	
	public function addEntrainement($id_competence, $nom_competence) {
		$this->xp->addEntrainement($id_competence, $nom_competence);
	}
}
?>
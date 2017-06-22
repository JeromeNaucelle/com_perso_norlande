<?php

defined('_JEXEC') or die;

class BonusLigneeChecker {
	
	private $bonusOccultisme;
	private $bonusIntrigue;
	private $bonusSociete;
	private $bonusBelligerance;
	
	public function __construct()
	{
		$this->bonusOccultisme = array("Loups Noirs", "Corbeaux de Tempête",
			"Ours du Couchant","Dragons de l'Onde", "Ecclesia");
			
		$this->bonusIntrigue = array("Loups Noirs", "Corbeaux de Tempête",
			"Serpents de Sinople","Junte de Sankta", "Lionnes d'Airin", "Terres Boriennes");
			
		$this->bonusSociete = array("Chiens du Lac","Serpents de Sinople",
			"Junte de Sankta","Capitannerie de Boulonnie", "Ecclesia");
		
		$this->bonusBelligerance = array("Chiens du Lac", "Capitannerie de Boulonnie",
			"Ours du Couchant", "Lionnes d'Airin", "Dragons de l'Onde", "Terres Boriennes");
		
	}
	
	public function hasBonusOccultisme($lignee) {
		return in_array($lignee, $this->bonusOccultisme);
	}
	
	public function hasBonusIntrigue($lignee) {
		return in_array($lignee, $this->bonusIntrigue);
	}
	
	public function hasBonusSociete($lignee) {
		return in_array($lignee, $this->bonusSociete);
	}
	
	public function hasBonusBelligerance($lignee) {
		return in_array($lignee, $this->bonusBelligerance);
	}
	
}
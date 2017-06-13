<?php

class Monnaie {
	
	public $piecesOr;
	public $piecesArgent;
	public $piecesCuivre;
	
	function __construct() {
		$this->piecesOr = 0;
		$this->piecesArgent = 0;
		$this->piecesCuivre = 0;
	}
	
	public function getFormatedText() {
		$ret = array();
		
		if($this->piecesOr > 0) {
			$ret[] = $this->piecesOr . " pièces d'or";
		}
		if($this->piecesArgent > 0) {
			$ret[] = $this->piecesArgent . " pièces d'argent";
		}
		if($this->piecesCuivre > 0) {
			$ret[] = $this->piecesCuivre . " pièces de cuivre";
		}
		
		if(count($ret) == 0) {
			$ret[] = "Néant";
		}
		
		return implode("<br>", $ret);;
	}
	
	public function add($monnaie) {
		$this->piecesOr += $monnaie->piecesOr;
		$this->piecesArgent += $monnaie->piecesArgent;
		$this->piecesCuivre += $monnaie->piecesCuivre;
	}
}


?>
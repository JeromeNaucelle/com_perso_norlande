<?php

include(JPATH_COMPONENT . '/includes/config.php');

defined('_JEXEC') or die;

define("OCCULTISME", "occultisme");
define("SOCIETE", "societe");
define("INTRIGUE", "intrigue");
define("BELLIGERANCE", "belligerance");
define("INCOLORE", "incolores");


class Lignees {
	public static $lignees = array ("Chiens du Lac", "Corbeaux de Tempête", "Lionnes d'Airin",
	"Ours du Couchant", "Serpents de Sinople", "Capitannerie de Boulonnie", "Dragons de l'Onde", 
	"Loups Noirs", "Junte de Sankta", "Piraterie des 5 mers", "Ecclesia", "Terres Boriennes");
	
	public static function getOrgaMail($lignee) {
		$mail = "";
		switch($lignee) {
			case "Chiens du Lac":
				$mail = MAIL_CHIENS_LAC;
				break;
				
			case "Corbeaux de Tempête":
				$mail = MAIL_CORBEAUX_TEMPETE;
				break;
				
			case "Lionnes d'Airin":
				$mail = MAIL_LIONNES_AIRIN;
				break;
				
			case "Ours du Couchant":
				$mail = MAIL_OURS_COUCHANT;
				break;
				
			case "Serpents de Sinople":
				$mail = MAIL_SERPENT_SINOPLE;
				break;
				
			case "Capitannerie de Boulonnie":
				$mail = MAIL_CAPITANNERIE_BOULONNIE;
				break;
				
			case "Dragons de l'Onde":
				$mail = MAIL_DRAGONS_ONDE;
				break;
				
			case "Loups Noirs":
				$mail = MAIL_LOUPS_NOIRS;
				break;
				
			case "Junte de Sankta":
				$mail = MAIL_JUNTE_SANKTA;
				break;
				
			case "Piraterie des 5 mers":
				$mail = MAIL_PIRATERIE_5_MERS;
				break;
				
			case "Ecclesia":
				$mail = MAIL_ECCLESIA;
				break;
				
			case "Terres Boriennes":
				$mail = MAIL_TERRES_BORIENNES;
				break;
				
		return $mail;
		}
	}
	
}

class Familles {
	public static $familles = array (OCCULTISME, SOCIETE, INTRIGUE, BELLIGERANCE);
	
}

?>
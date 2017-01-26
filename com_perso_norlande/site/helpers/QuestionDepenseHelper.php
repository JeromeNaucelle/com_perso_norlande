<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/includes/Perso.php';


class QuestionDepenseHelper {
	
	/*
	Types d'XP possibles
		$xp = {niveau_competence:3,cristaux:{incolore:2, occultisme:3}, entrainement:{12:"Maitre des poisons", 20:"Maitre des anges"}};
		$xp = {entrainement:{12:"Maitre des poisons", 20:"Maitre des anges"}};
		$xp = {cristaux:{incolore:2, occultisme:3}};
		
	Forme du formulaire de question:
	
	<div id="question_dep_xp" style="display:none; cursor: default;max-width:300px;">
		<input type="hidden" name="niveauCompetence" id="niveauCompetence"></input>
		
		<p style="text-align:center">Pour acqu&eacute;rir cette comp&eacute;tence vous devez :</p>
		<form id="depense_points_creation">
			<fieldset>
			<legend>Points de cr&eacute;ation</legend>
			<input type="hidden" name="typeXp" value="points_creation"></input>
	
			<input type="button" value="Valider" id="submit_points_creation" onclick="checkNbPointsCreation()"/>
			</fieldset>
		</form>	
		
		<form id="depense_cristaux">
			<fieldset>
			<legend>Cristaux</legend>
			<input type="hidden" name="typeXp" value="cristaux"></input>
			
			<input type="button" value="Valider" id="submit_cristaux" onclick="checkNbCristaux()"/>
			</fieldset>
		</form>
		
		
		<form method="post" id="depense_entrainement">
			<fieldset>
			<legend>Entrainements</legend>
			<input type="hidden" name="typeXp" value="entrainement"></input>
			<p>Utiliser un entrainement :</p>
			<input type="button" value="Valider" id="submit_entrainement" onclick="postChoixDepenseXP('depense_entrainement')">
			</fieldset>
		</form>
		<input type="button" value="Annuler" style="width:100%" onclick="cancelDepenseCristaux()"/>
	</div>	
	*/
	
	
	private static $questionPc = '<form id="depense_points_creation">
		<fieldset>
		<legend>Points de cr&eacute;ation</legend>
		<input type="hidden" name="typeXp" value="points_creation"></input>
		<p>Dépenser %d points de création parmi vos points suivants :</p>
		<label for="dep_points_creation">Points de création : </label><input type="text" name="dep_points_creation" value="0" class="shortNb"> / %d<br>
		<input type="button" value="Valider" id="submit_points_creation" onclick="checkNbPointsCreation()"/>
		</fieldset>
	</form>';
	
	private static $questionCristaux = '<form id="depense_cristaux">
		<fieldset>
		<legend>Cristaux</legend>
		<input type="hidden" name="typeXp" value="cristaux"></input>
		<p>Dépenser %d cristaux parmi les cristaux suivants :</p>
		%s
		<input type="button" value="Valider" id="submit_cristaux" onclick="checkNbCristaux()"/>
		</fieldset>
	</form>';
	
	
	private static $questionEntrainements = '<form method="post" id="depense_entrainement">
		<fieldset>
		<legend>Entrainements</legend>
		<input type="hidden" name="typeXp" value="entrainement"></input>
		<p>Utiliser un entrainement :</p>
		%s
		<input type="button" value="Valider" id="submit_entrainement" onclick="postChoixDepenseXP(\'depense_entrainement\')">
		</fieldset>
	</form>';
	
	private static $cancelDepenseButton = '
		<input type="button" value="Annuler" style="width:100%" onclick="cancelDepenseCristaux()"/>';
	
	private static $alert = '<p>%s</p>
        <input type="button" onclick="cancelDepenseCristaux()" value="OK" />';
	
	public static function getQuestionDepenseXp($data) {
		$html = "";
		if($data["result"] == -1 
				|| $data["result"] == 4 ) {
			return sprintf(QuestionDepenseHelper::$alert, $data["msg"]);
		}
		
		$xp = $data["xp"];
		$niveau = $data["niveauCompetence"];
		$html = "<input type=\"hidden\" value=\"$niveau\" id=\"niveauCompetence\"></input>";
		
		if(array_key_exists("points_creation", $xp)
			&& $xp["points_creation"] >= $niveau) {
				$html = html . sprintf(QuestionDepenseHelper::$questionPc, $niveau, $xp["points_creation"]);
				$html = $html . QuestionDepenseHelper::$cancelDepenseButton;
				return $html;
		}
		
		if( array_key_exists("cristaux", $xp) ) {
				$cristaux_fields = "";
				foreach($xp["cristaux"] as $type => $nb) {
					$cristaux_fields = $cristaux_fields . "<label for=\"dep_cristaux_$type\">$type : </label><input type=\"text\" name=\"dep_cristaux_$type\" value=\"0\" class=\"shortNb\"> / $nb<br>";
				}
				$html = $html . sprintf(QuestionDepenseHelper::$questionCristaux, $niveau, $cristaux_fields);
		}
		
		if (array_key_exists("cristaux", $xp) 
				&& array_key_exists("entrainement", $xp)) {
			$html = $html . '<p style="text-align:center"><b>ou</b></p>';
		}
		
		if( array_key_exists("entrainement", $xp) ) {
				$entrainement_fields = "";
				foreach($xp["entrainement"] as $id => $nom) {
					$entrainement_fields = $entrainement_fields . "<input type=\"radio\" name=\"dep_entrainement_group\" value=\"$id\">$nom<br>";
				}
				$html = $html . sprintf(QuestionDepenseHelper::$questionEntrainements, $entrainement_fields);
		}
		
		$html = $html . QuestionDepenseHelper::$cancelDepenseButton;
		return $html;
	}
	
}

?>
<?php

use Odtphp\Odf;

include('components/com_perso_norlande/helpers/template.php');
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();
$encoding = "UTF-8";

//$doc->addStyleSheet("components/com_perso_norlande/media/perso_norlande/css/fiche_perso.css",'text/css',"screen");

require_once 'odtphp/vendor/autoload.php';

$odf = new Odf("components/com_perso_norlande/views/voirfiche/tmpl/template_fiche_perso.odt");

	$armure = $this->perso->getArmure();
	$monnaie = $this->perso->getMonnaie();
	$monnaie->add($this->synthese->getMonnaie());
  		
  	$odf->setVars('lignee', $this->perso->getLignee());
	$odf->setVars('anciennete', $this->perso->getAnciennete());
	$odf->setVars('nom_perso', htmlentities($this->perso->getNom()));
	$odf->setVars('mana', $this->synthese->getMana());
	$odf->setVars('coups', $this->synthese->getCoups($armure));
	$odf->setVars('force_physique', $this->synthese->getForcePhysique());
	$odf->setVars('niveau_langue', $this->synthese->getNiveauLangue());
	$odf->setVars('lieux_pouvoir', $this->synthese->getLieuxPouvoir("\n"));
	$odf->setVars('esquive', $this->synthese->getEsquive($armure));
	$odf->setVars('resiste', $this->synthese->getResiste($armure));
	$odf->setVars('immunite', $this->synthese->getImmunite($armure, "\n"));
	$odf->setVars('armure', $armure);
	$odf->setVars('monnaie', $monnaie->getFormatedText("\n"));
	$odf->setVars('histoire', htmlentities($this->perso->getHistoire()));
	
	foreach($this->competencesClassees as $classement) {
		for($i=1; $i<5; $i++) {
			$odf->setVars('comp_'.$classement->getFamille().'_lvl'.$i, 
				$classement->getFromLevel($i, "\n"));
		}
	}
	
	
	$segPiegesEtTechniques = $odf->setSegment('technique');
	$piegeEtTechniques = $this->synthese->getPiegesEtTechniques();
  	foreach($piegeEtTechniques as $piege) {
		$segPiegesEtTechniques->nom($piege->nom);
		$segPiegesEtTechniques->cout($piege->cout);
		$segPiegesEtTechniques->effet($piege->effet);
		$segPiegesEtTechniques->merge();
	}
	$odf->mergeSegment($segPiegesEtTechniques);
	
	$segManiements = $odf->setSegment('maniement');
	$maniements = $this->synthese->getManiements();
  	foreach($maniements as $maniement) {
		$segManiements->nom($maniement);
		$segManiements->merge();
	}
	$odf->mergeSegment($segManiements);
	
	
	$segMetamorphoses = $odf->setSegment('metamorphose');
	$metamorphoses = $this->synthese->getMetamorphoses();
  	foreach($metamorphoses as $metamorphose) {
  		$segMetamorphoses->cout($metamorphose->cout);
  		$segMetamorphoses->effet($metamorphose->effet);
		$segMetamorphoses->merge();
  	}
  	$odf->mergeSegment($segMetamorphoses);
  	
  	
  	$segConnaissances = $odf->setSegment('connaissance');
	$connaissances = $this->synthese->getConnaissances();
  	foreach($connaissances as $connaissance) {
  		$segConnaissances->nom($connaissance);
		$segConnaissances->merge();
  	}
  	$odf->mergeSegment($segConnaissances);
  	
  	
  	$segSortileges = $odf->setSegment('sortilege');
	$sortileges = $this->synthese->getSortileges();
  	foreach($sortileges as $sortilege) {
		$formule = $this->edit_orga ? $sortilege->formule : "« ??? »";
  		
  		$segSortileges->nom($sortilege->nom);
  		$segSortileges->formule($formule);
  		$segSortileges->effet($sortilege->effet);
		$segSortileges->merge();
  	}
  	$odf->mergeSegment($segSortileges);
  	
  	
  	$segSortsMasse = $odf->setSegment('sort_masse');
	$sortsMasse = $this->synthese->getSortsMasse();
  	foreach($sortsMasse as $sortMasse) {
		$formule = $this->edit_orga ? $sortMasse->formule : "« ??? »";
  		
  		$segSortsMasse->nom($sortMasse->nom);
  		$segSortsMasse->formule($formule);
  		$segSortsMasse->effet($sortMasse->effet);
		$segSortsMasse->merge();
  	}
  	$odf->mergeSegment($segSortsMasse);
  	
  	
  	$segAidesJeu = $odf->setSegment('aide_jeu');
	$aides = $this->synthese->getAideJeu();
  	foreach($aides as $aide) {
  		$segAidesJeu->nom($aide);
		$segAidesJeu->merge();
  	}
  	$odf->mergeSegment($segAidesJeu);
  	
  	
  	$segPossessions = $odf->setSegment('possession');
	$possessions = $this->synthese->getPossessionsDepart();
  	foreach($possessions as $possession) {  		
  		$segPossessions->nom($possession);
		$segPossessions->merge();
  	}
  	$odf->mergeSegment($segPossessions);
  	
  	
  	$segCapaOccultes = $odf->setSegment('capa_occulte');
	$breuvagesEtInvocations = $this->synthese->getBreuvagesEtInvocations();
  	foreach($breuvagesEtInvocations as $breuvage) {
  		$segCapaOccultes->nom($breuvage->nom);
  		$segCapaOccultes->cout($breuvage->cout);
  		$segCapaOccultes->effet($breuvage->effet);
		$segCapaOccultes->merge();
  	}
  	$odf->mergeSegment($segCapaOccultes);
  	
  	
  	$segPouvoirs = $odf->setSegment('pouvoir');
	$pouvoirs = $this->synthese->getPouvoirsMagiques();
  	foreach($pouvoirs as $pouvoir) {
		$formule = $this->edit_orga ? $pouvoir->formule : "« ??? »";
  		
  		$segPouvoirs->nom($pouvoir->nom);
  		$segPouvoirs->formule($formule);
  		$segPouvoirs->effet($pouvoir->effet);
		$segPouvoirs->merge();
  	}
  	$odf->mergeSegment($segPouvoirs);
  	
  	
  	$segAmeliorations = $odf->setSegment('amelioration');
	$ameliorations = $this->synthese->getAmeliorations();
  	foreach($ameliorations as $amelioration) {
  		$segAmeliorations->nom($amelioration);
		$segAmeliorations->merge();
  	}
  	$odf->mergeSegment($segAmeliorations);
  	
  	
  	$segCapacites = $odf->setSegment('capacite');
	$capacites = $this->synthese->getCapacites();
  	foreach($capacites as $capacite) {
  		$segCapacites->effet($capacite->effet);
  		$segCapacites->frequence($capacite->frequence);
		$segCapacites->merge();
  	}
  	$odf->mergeSegment($segCapacites);
  	
  	
  	$segObjetsAPrevoir = $odf->setSegment('a_prevoir');
	$objsAPrevoir = $this->synthese->getObjetsAPrevoir();
  	foreach($objsAPrevoir as $obj) {
  		$segObjetsAPrevoir->nom($obj);
		$segObjetsAPrevoir->merge();
  	}
  	$odf->mergeSegment($segObjetsAPrevoir);
  	
  	
  	$segParcelles = $odf->setSegment('parcelle');
	$parcelles = $this->synthese->getParcelles();
  	foreach($parcelles as $parcelle) {
  		$segParcelles->nom($parcelle);
		$segParcelles->merge();
  	}
  	$odf->mergeSegment($segParcelles);
  	
  	
  	$segLangues = $odf->setSegment('langue');
	$langues = $this->synthese->getSyntheseLangue();
  	foreach($langues as $langue) {
  		$segLangues->nom($langue);
		$segLangues->merge();
  	}
  	$odf->mergeSegment($segLangues);
  	
  	
  	$segAttaquesSpe = $odf->setSegment('attaque_spe');
  	$attaques_spe = $this->synthese->getAttaquesSpe();
	$typesAttaques = $attaques_spe->getTypes();
  	foreach($typesAttaques as $type => $labelType) {
  		
  		$attaques = $attaques_spe->getAttaques($type);
  		
		foreach($attaques as $attaque) {
			$segAttaquesSpe->type($labelType);
			$segAttaquesSpe->effet($attaque);
			$segAttaquesSpe->merge();
		}  		
  		
  	}
  	$odf->mergeSegment($segAttaquesSpe);


// We export the file
$odf->exportAsAttachedFile();

JFactory::getApplication()->close();

?>

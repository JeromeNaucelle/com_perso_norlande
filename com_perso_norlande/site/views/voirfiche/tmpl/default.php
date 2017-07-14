<?php

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

$doc->addStyleSheet("components/com_perso_norlande/media/perso_norlande/css/fiche_perso.css",'text/css',"screen");


$template = new Template('./');

  // modèle à utiliser auquel on adjoint un nom arbitraire
  

  $template->set_filenames(array(
    'body' => 'components/com_perso_norlande/views/voirfiche/tmpl/fichePerso.html'
  ));


	foreach($this->competencesClassees as $classement) {
		$template->assign_vars(array(
    'comp_'.$classement->getFamille().'_lvl1' => $classement->getFromLevel(1),
    'comp_'.$classement->getFamille().'_lvl2' => $classement->getFromLevel(2),
	 'comp_'.$classement->getFamille().'_lvl3' => $classement->getFromLevel(3),
	 'comp_'.$classement->getFamille().'_lvl4' => $classement->getFromLevel(4)
  		));
	}
	
	$armure = $this->perso->getArmure();
	$monnaie = $this->perso->getMonnaie();
	$monnaie->add($this->synthese->getMonnaie());
	$reliquat = str_replace("\n", '<br>', $this->perso->getReliquat());
	$lecture_ecriture = $this->synthese->getLectureEcriture() == true ? "Vous savez lire et écrire" : "Parlées";
	
	$template->assign_vars(array(
	 'lignee' => $this->perso->getLignee(),
	 'nom_perso' => htmlentities($this->perso->getNom()),
    'mana' => $this->synthese->getMana(),
    'coups' => $this->synthese->getCoups($armure),
    'force_physique' => $this->synthese->getForcePhysique(),
    'niveau_langue' => $this->synthese->getNiveauLangue(),
    'lieux_pouvoir' => $this->synthese->getLieuxPouvoir(),
    'esquive' => $this->synthese->getEsquive($armure),
    'resiste' => $this->synthese->getResiste($armure),
    'immunite' => $this->synthese->getImmunite($armure),
    'armure' => $armure,
    'monnaie' => $monnaie->getFormatedText(),
    'anciennete' => $this->perso->getAnciennete(),
    'reliquat' => $reliquat,
    'lecture_ecriture' => $lecture_ecriture,
    'histoire' => htmlentities($this->perso->getHistoire())
  		));
  		
  		
  	$piegeEtTechniques = $this->synthese->getPiegesEtTechniques();
  	foreach($piegeEtTechniques as $piege) {
  		
	  	$template->assign_block_vars('technique', array(
	    'nom' => $piege->nom,
	    'cout' => $piege->cout,
	    'effet' => $piege->effet
	  		));
  	}
  	
  	$maniements = $this->synthese->getManiements();
  	foreach($maniements as $maniement) {
  		
	  	$template->assign_block_vars('maniement', array(
	    'nom' => $maniement
	  		));
  	}
  	
  	$metamorphoses = $this->synthese->getMetamorphoses();
  	foreach($metamorphoses as $metamorphose) {
  		
	  	$template->assign_block_vars('metamorphose', array(
	    'cout' => $metamorphose->cout,
	    'effet' => $metamorphose->effet
	  		));
  	}
  	
  	$connaissances = $this->synthese->getConnaissances();
  	foreach($connaissances as $connaissance) {
  		
	  	$template->assign_block_vars('connaissance', array(
	    'nom' => $connaissance
	  		));
  	}
  	
  	$sortileges = $this->synthese->getSortileges();
  	foreach($sortileges as $sortilege) {
  		
	  	$template->assign_block_vars('sortilege', array(
	    'nom' => $sortilege->nom,
	    'formule' => $this->edit_orga ? $sortilege->formule : "« ??? »",
	    'effet' => $sortilege->effet
	  		));
  	}
  	
  	$sortsMasse = $this->synthese->getSortsMasse();
  	foreach($sortsMasse as $sortMasse) {
  		
	  	$template->assign_block_vars('sort_masse', array(
	    'nom' => $sortMasse->nom,
	    'formule' => $this->edit_orga ? $sortMasse->formule : "« ??? »",
	    'effet' => $sortMasse->effet
	  		));
  	}
  	
	$aides = $this->synthese->getAideJeu();
  	foreach($aides as $aide) {
  		
	  	$template->assign_block_vars('aide_jeu', array(
	    'nom' => $aide
	  		));
  	}
  	
  	$possessions = $this->synthese->getPossessionsDepart();
  	foreach($possessions as $possession) {
  		
	  	$template->assign_block_vars('possession', array(
	    'nom' => $possession
	  		));
  	}
  	
  	$breuvagesEtInvocations = $this->synthese->getBreuvagesEtInvocations();
  	foreach($breuvagesEtInvocations as $breuvage) {
  		
	  	$template->assign_block_vars('capa_occulte', array(
	    'nom' => $breuvage->nom,
	    'cout' => $breuvage->cout,
	    'effet' => $breuvage->effet
	  		));
  	}
  	
	$pouvoirs = $this->synthese->getPouvoirsMagiques();
  	foreach($pouvoirs as $pouvoir) {
  		
	  	$template->assign_block_vars('pouvoir', array(
	    'nom' => $pouvoir->nom,
	    'formule' => $this->edit_orga ? $pouvoir->formule : "« ??? »",
	    'effet' => $pouvoir->effet
	  		));
  	}
  	
  	$ameliorations = $this->synthese->getAmeliorations();
  	foreach($ameliorations as $amelioration) {
  		
	  	$template->assign_block_vars('amelioration', array(
	    'nom' => $amelioration
	  		));
  	}
  	
  	$capacites = $this->synthese->getCapacites();
  	foreach($capacites as $capacite) {
  		
	  	$template->assign_block_vars('capacite', array(
	    'effet' => $capacite->effet,
	    'frequence' => $capacite->frequence
	  		));
  	}
  	
  	$objsAPrevoir = $this->synthese->getObjetsAPrevoir();
  	foreach($objsAPrevoir as $obj) {
  		
	  	$template->assign_block_vars('a_prevoir', array(
	    'nom' => $obj
	  		));
  	}
  	
  	$parcelles = $this->synthese->getParcelles();
  	foreach($parcelles as $parcelle) {
  		
	  	$template->assign_block_vars('parcelle', array(
	    'nom' => $parcelle
	  		));
  	}
  	
  	$langues = $this->synthese->getSyntheseLangue();
  	foreach($langues as $langue) {
  		
	  	$template->assign_block_vars('langue', array(
	    'nom' => $langue
	  		));
  	}
  	
  	$attaques_spe = $this->synthese->getAttaquesSpe();
  	$typesAttaques = $attaques_spe->getTypes();
  	foreach($typesAttaques as $type => $labelType) {
  		$attaques = $attaques_spe->getAttaques($type);
  		
		foreach($attaques as $attaque) {
		  	$template->assign_block_vars('attaque_spe', array(
		  		'type' => $labelType,
		   	'nom' => $attaque
		  		));
		}
  	}
	
  // Affichage des données

  $template->pparse('body');
?>

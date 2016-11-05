<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * Hello World Component Controller
 *
 * @since  0.0.1
 */
class Perso_NorlandeController extends JControllerLegacy
{
	
	public function getArbreMaitrise() 
	{	
		$mainframe = JFactory::getApplication();
		
		$model = null;
		$model = $this->getModel('creationperso');
		
		$jinput = JFactory::getApplication()->input;
		$competence_id = $jinput->get('competence', '0', 'INT');
		$arbre_maitrise = $model->getArbreMaitrise($competence_id);
		
		echo json_encode($arbre_maitrise);  
		$mainframe->close();
	}
	
	
	
	public function userSelect()
	{
		$mainframe = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$result = array("result" => "competence ID invalide");
		$competence_id = $jinput->get('competence', '0', 'INT');
		if($competence_id == 0)
		{
			echo json_encode($result);
			$mainframe->close();
			return;
		}
		
		
		/*
		$session = JFactory::getSession();
		$session->get( 'perso', 'empty' );
		
		$model = null;
		$model = $this->getModel('creationperso');
		
		$jinput = JFactory::getApplication()->input;
		$json = json_decode($jinput, true); 
		
		$maitrise = $json['maitrise'];
		if($maitrise == null)
		{
			//TODO
		}
		$arbre_maitrise = $model->getArbreMaitrise($maitrise);
		
		echo json_encode($arbre_maitrise);  
		*/
		$test = array("result"=> $competence_id);
		echo json_encode($test);
		$mainframe->close();
	}
}

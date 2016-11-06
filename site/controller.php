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

require_once JPATH_COMPONENT . '/includes/Perso.php';
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
		
		
		
		$session = JFactory::getSession();
		$perso = unserialize($session->get( 'perso', 'empty' ));
		if($perso === 'empty')
		{
			$result["result"] = "Personnage non trouvé dans la session";
		}
		
		$model = null;
		$model = $this->getModel('creationperso');
		$arbre = $model->getArbreMaitrisePhp($competence_id);
		$result["result"] = $perso->can_develop($competence_id, $arbre);
		
		echo json_encode($result);
		$mainframe->close();
	}
}

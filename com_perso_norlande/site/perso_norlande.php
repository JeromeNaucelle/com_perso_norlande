<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access'); 

// Get an instance of the controller prefixed by HelloWorld
$user = JFactory::getUser();
$status = $user->guest;

if($status == 1){
	$allDone =& JFactory::getApplication();
	$allDone->redirect('index.php');
}
else
{
	//do user logged in stuff
	$controller = JControllerLegacy::getInstance('Perso_Norlande'); 
	
	$session = JFactory::getSession();
	$persoId = $session->get( 'perso_id', -1 );
	
	if($persoId == -1) {
		$persoId = PersoHelper::getPersoIdFromUser($user->id);
	}		
		
	if($persoId != -1) {
		$session->set( 'perso_id', $persoId );
	}
	
	// Perform the Request task
	$input = JFactory::getApplication()->input;
	$controller->execute($input->getCmd('task')); 
	
	// Redirect if set by the controller
	$controller->redirect();
} 



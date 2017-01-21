<?php
 
defined('_JEXEC') or die();
 
class TablePersos extends JTable
{
	
	public function __construct(&$db)
	{
		parent::__construct( 'persos', 'id', $db );
	}
}
<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


class CommonHelper {
	
	public static function getEnumValues( $table, $field )
	{
		$db = JFactory::getDbo();
		$sql = "SHOW COLUMNS FROM ".$db->quoteName($table)." WHERE Field = ".$db->quote($field);
		$db->setQuery($sql);
		$type = $db->loadAssocList()[0]['Type'];
		preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
		$enum = explode("','", $matches[1]);
		return $enum;
	}
	
}

?>
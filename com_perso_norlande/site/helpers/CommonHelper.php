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
	
	
	/* Hack pour récupérer les nom de columns sous la forme
	* `a`.`colonne` car MySQL 5.6 ne gère pas la forme `a.*`
	*/
	public static function getColumnsName( $table, $prefix )
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('COLUMN_NAME'));
		$query->from($db->quoteName('INFORMATION_SCHEMA').'.'.$db->quoteName('COLUMNS'));
		$query->where($db->quoteName('TABLE_NAME') . ' = ' . $db->quote($table));
		
		$db->setQuery($query);
		$columns_competences = $db->loadColumn();
		for($i = 0; $i < count($columns_competences); $i+=1) {
			$column = $columns_competences[$i];
			$columns_competences[$i] = $db->quoteName($prefix).'.'.$db->quoteName($column);
		}
		return $columns_competences;
	}
	
}

?>
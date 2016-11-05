<?php

defined('_JEXEC') or die;

class Noeud {
	
	private $data;
	private $parent;
	private $fils1;
	private $fils2;
	
	public function __construct($competence)
	{
		$this->data = $competence;
		$this->fils1 = NULL;
		$this->fils2 = NULL;
		$this->parent = NULL;
	}
	
	public function setFils($fils)
	{
		if(is_null($this->fils1))
		{
			$this->fils1 = $fils;
			return;
		}
		
		if(is_null($this->fils2))
		{
			$this->fils2 = $fils;
		} 
		else 
		{
			JLog::add(JText::_("Affectation d'un fils supplémentaire impossible"), JLog::WARNING, 'jerror');
			JLog::add(JText::_("fils1 : ".$this->fils1->data->getNom()), JLog::WARNING, 'jerror');
			JLog::add(JText::_("fils2 : ".$this->fils2->data->getNom()), JLog::WARNING, 'jerror');
			JLog::add(JText::_("à ajouter : ".$fils->data->getNom()), JLog::WARNING, 'jerror');
		}
		
	}
	
	public function setData($data)
	{
		$this->data = $data;
	}
	
	public function setParent($parent)
	{
		$this->parent = $parent;
	}
	
	public function fils1()
	{
		return $this->fils1();
	}
	
	public function fils2()
	{
		return $this->fils2();
	}
	
	public function parent()
	{
		return $this->parent;
	}
	
	public function data()
	{
		return $this->data;
	}
}

class Arbre {
	
	private $root;
	private $table;
	
	public function __construct($query_result)
	{
		for($i=0; $i<count($query_result); $i++)
		{
			$competence = new Competence($query_result[$i]);
			
			$noeud = new Noeud($competence);
			$this->table[$competence->getNomFormat()] = $noeud;
		}
		
		// Creation des liens parent/enfants
		for($i=0; $i<count($table); $i++)
		{
			$cur_node = $this->table[$i];
			$competence_requise = $cur_node->data()->getRequis();
			if(!is_empty($competence_requise)) {
				$parent_node = $this->table[$competence_requise];
				$cur_node->setParent($parent_node);
				$parent_node->setFils($cur_node);
			}
			else 
			{
				$root = $cur_node;
			}
		}
		
	}
	
}

?>
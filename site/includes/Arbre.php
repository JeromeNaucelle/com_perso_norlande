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
	
	protected $root;
	protected $table;
	
	public function __construct($query_result)
	{
		error_log("Construction de l'arbre");
		for($i=0; $i<count($query_result); $i++)
		{
			$competence = Competence::create($query_result[$i]);
			//error_log("Arbre() : ajout competence ".$competence->getNom());
			
			$noeud = new Noeud($competence);
			$this->table[$competence->getId()] = $noeud;
		}
		
		// Creation des liens parent/enfants
		foreach($this->table as $competence_id => $competence)
		{
			$cur_node = $this->table[$competence_id];
			$competence_requise = $cur_node->data()->getParentId();
			if($competence_requise != 0) 
			{
				$parent_node = $this->table[$competence_requise];
				$cur_node->setParent($parent_node);
				$parent_node->setFils($cur_node);
			}
			else 
			{
				error_log("Arbre() racine trouvee : ".$competence->data()->getNom());
				$this->root = $cur_node;
			}
		}
		
	}
	
}

class ArbreMaitrise extends Arbre
{
	public function get_famille_maitrise() {	
		return $this->root->data()->getFamille();
	}
	
	public function getMaitrise()
	{
		return $this->root->data()->getMaitrise();
	}
	
	public function getCompetence($competence_id)
	{
		return $this->table[$competence_id]->data();;
	}
	
	public function getPathForCompetence($competenceId)
	{
		error_log("getPathForCompetence");
		$result = array();
		$i = 0;
		$node = $this->table[$competenceId];
		while($node->parent() != NULL)
		{
			$node = $node->parent();
			$result[$i] = $node->data()->getId();
			$i++;
		}
		return $result;
	}
	
	public function isEntrainementFor($entrainementId, $competenceId)
	{
		if(!array_key_exists ( $entrainementId , $this->table )) {
			return false;
		}
		$node = $this->table[$entrainementId];
		while($node->parent() != NULL)
		{
			if($node->data()->getId() == $competenceId) {
				return true;
			}
			$node = $node->parent();
		}	
		return false;
	}
}

?>
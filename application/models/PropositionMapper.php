<?php
//"Default" est le namespace défini dans le bootstrap
class Default_Model_PropositionMapper
{

	protected $_dbTable;

	//settor
	public function setDbTable($dbTable)
	{
		if (is_string($dbTable)) {
			$dbTable = new $dbTable();
		}
		if (!$dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_dbTable = $dbTable;
		return $this;
	}

	//guettor
	public function getDbTable()
	{
		if (null === $this->_dbTable) {
			$this->setDbTable('Default_Model_DbTable_Proposition');
		}
		return $this->_dbTable;
	}

	//sauve une nouvelle entrée dans la table
	public function save(Default_Model_Proposition $proposition)
	{
		//récupération dans un tableau des données de l'objet $proposition
		//les noms des clés du tableau correspondent aux noms des champs de la table
		$data = array(
               	'id' => $proposition->getId(),
               	'id_personne' => $proposition->getId_personne(),
               	'date_debut' => $proposition->getDate_debut(),
               	'mi_debut_journee' => $proposition-> getMi_debut_journee(),
			   	'date_fin' => $proposition->getDate_fin(),
				'mi_fin_journee' => $proposition->getMi_fin_journee(),
				'nombre_jours' => $proposition->getNombre_jours(),
				'etat' => $proposition->getEtat()		
		);

		//on vérifie si un l'objet $proposition contient un id
		//si ce n'est pas le cas, il s'agit d'un nouvel enregistrement
		//sinon, c'est une mise à jour d'une entrée à effectuer
		if(null === ($id = $proposition->getId()))
		{
			unset($data['id']);
			$this->getDbTable()->insert($data);
		}
		else
		{
			$this->getDbTable()->update($data, array('id = ?' => $id));
		}
	}

	//récupére une entrée dans la table
	public function find($id, Default_Model_Proposition $proposition)
	{
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return;
		}

		//initialisation de la variable $row avec l'entrée récupérée
		$row = $result->current();

		//setting des valeurs dans notre objet $proposition passé en argument
		$proposition->setId($row->id);
		$proposition->setId_personne($row->id_personne);
		$proposition->setDate_debut($row->date_debut);
		$proposition->setMi_debut_journee($row->mi_debut_journee);
		$proposition->setDate_fin($row->date_fin);
		$proposition->setMi_fin_journee($row->mi_fin_journee);
		$proposition->setNombre_jours($row->nombre_jours);
		$proposition->setEtat($row->etat);
	}
	
	
 // recuperer les conges dans une periode de temps pour une ressource donn�e 
    //$flag = 1 inclue les bornes / $flag = 0 n'iclue pas les bornes 
	public function getProposition($id_personne,$date_debut,$date_fin,$flag) 
	    {  
	    	
	    	$debut_mois = date('Y-m-d',strtotime($date_debut)); 
    	    $fin_mois = date('Y-m-d',strtotime($date_fin)).'23:59:59';
    	    
	    	
		    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
		    $select = new Zend_Db_Select($db);
		    $select ->from((array('p' =>'proposition')),array('p.id_personne' ,'p.date_debut','p.date_fin','p.mi_debut_journee','p.mi_fin_journee','p.nombre_jours')); 
		    $select->where('p.id_personne ='.$id_personne);
		   
		    
	        if($flag == 0) // retourne toute les dates qui touche la periode 
	        {
		     
	        	$select->where('p.date_debut >= ?', $debut_mois) 
    	               ->where('p.date_fin <= ?', $fin_mois);
			    $row = $select->query()->fetchAll();
	        	
	        }
	        elseif($flag == 1) // retourne toute les dates qui touche la periode sauf date_debut == df  ou date_fin == dd 
	        {
		      
		        $select->where('p.date_debut > ?', $debut_mois) 
    	               ->where('p.date_fin < ?', $fin_mois);
			    $row = $select->query()->fetchAll();
		        
			    
	        }
	
	    	return $row;
	    }
	

	//récupére toutes les entrées de la table
	public function fetchAll($str,$where=null)
	{
		//récupération dans la variable $resultSet de toutes les entrées de notre table
		$resultSet = $this->getDbTable()->fetchAll($where);

		//chaque entrée est représentée par un objet Default_Model_Proposition
		//qui est ajouté dans un tableau
		$entries = array();
		foreach($resultSet as $row)
		{
			$entry = new Default_Model_Proposition();
			$entry->setId($row->id);
			$entry->setId_personne($row->id_personne);
			$entry->setDate_debut($row->date_debut);
			$entry->setMi_debut_journee($row->mi_debut_journee);
			$entry->setDate_fin($row->date_fin);
			$entry->setMi_fin_journee($row->mi_fin_journee);
			$entry->setNombre_jours($row->nombre_jours);
			$entry->setEtat($row->etat);
			$entry->setMapper($this);

			$entries[] = $entry;
		}

		return $entries;
	}

	//permet de supprimer un utilisateur,
	//reçoit la condition de suppression (le plus souvent basé sur l'id)
	public function delete($id)
	{
		$result = $this->getDbTable()->delete($id);
	}


	
	
	
}

<?php
//"Default" est le namespace d�fini dans le bootstrap
class Default_Model_SoldeMapper
{
	//$_dbTable va faire r�f�rence � un objet Zend_Db_Table_Abstract
	//dans notre cas la classe Default_Model_DbTable_Personne
	//du fichier application/models/DbTable/Personne.php
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
			$this->setDbTable('Default_Model_DbTable_Solde');
		}
		return $this->_dbTable;
	}

	//sauve une nouvelle entr�e dans la table
	public function save(Default_Model_Solde $solde)
	{
		//r�cup�ration dans un tableau des donn�es de l'objet $Personne
		//les noms des cl�s du tableau correspondent aux noms des champs de la table
		$data = array(
               	'id_personne' => $solde->getId_personne(),
				'total_q1' => $solde->getTotal_q1(),
				'total_q2' => $solde->getTotal_q2(),
				'total_cp' => $solde->getTotal_cp(),
				'annee_reference' => $solde->getAnnee_reference(),
				);
		

		//on v�rifie si un l'objet $solde contient un id
		//si ce n'est pas le cas, il s'agit d'un nouvel enregistrement
		//sinon, c'est une mise � jour d'une entr�e � effectuer
		if(null !==($id_personne = $solde->getId_personne()))
		{
			//unset($data['id_personne']);
			$this->getDbTable()->insert($data);
			
		}
	
	}

	//r�cup�re une entr�e dans la table
	public function find($id_personne,$annee_reference, Default_Model_Solde $solde)
	{
		$result = $this->getDbTable()->find($id_personne,$annee_reference);
		if (0 == count($result)) 
		{
			return;
		}
		$personne = new Default_Model_Personne();
		$personne = $personne->find($id_personne,$annee_reference);
		$date_entree = $personne->getDate_entree();
		$modalite = $personne->getId_modalite();
		//initialisation de la variable $row avec l'entr�e r�cup�r�e
		$row = $result->current();

		//setting des valeurs dans notre objet $Personne pass� en argument
			$solde->setId_personne($row->id_personne);
			$solde->setTotal_cp($date_entree);
			if ($modalite ==7)
			{
				$solde->setTotal_q2(0);
			}
			else $solde->setTotal_q2(0);
			$solde->setTotal_q1($modalite);
			
			$solde->setAnnee_reference();
			
	}

	//r�cup�re toutes les entr�es de la table
	public function fetchAll($str)
	{
		//r�cup�ration dans la variable $resultSet de toutes les entr�es de notre table
		$resultSet = $this->getDbTable()->fetchAll($str);
		$personne = new Default_Model_Personne();
		$personne = $personne->fetchall($str=array());
		
		//chaque entr�e est repr�sent�e par un objet Default_Model_Personne
		//qui est ajout� dans un tableau
		$entries = array();
		foreach($personne as $p)
		{
			$date_entree = $p->getDate_entree();
			$id_personne = $p->getId();
			$modalite = $p->getId_modalite();
			$entry = new Default_Model_Solde();
			$entry->setId_personne($id_personne);
			$entry->setTotal_cp($date_entree);
			$entry->setTotal_q1($modalite);
			$entry->setTotal_q2(0);
			$entry->setAnnee_reference($str);
			$entry->setMapper($this);
			$entries[] = $entry;
		}

		return $entries;
	}

	//permet de supprimer un utilisateur,
	//re�oit la condition de suppression (le plus souvent bas� sur l'id)
	public function delete($id_personne)
	{
		$result = $this->getDbTable()->delete($id_personne);
	}
	

	
	public function find2($id_personne,$annne_reference, Default_Model_Solde $solde)
	{
		$result = $this->getDbTable()->find($id_personne,$annne_reference);
		if (0 == count($result)) 
		{
			return;
		}

		//initialisation de la variable $row avec l'entr�e r�cup�r�e
		$row = $result->current();

		//setting des valeurs dans notre objet $Personne pass� en argument
			$solde->setId_personne($row->id_personne);
			$solde->setTotal_cp($row->total_cp);
			$solde->setTotal_q1($row->total_q1);
			$solde->setTotal_q2($row->total_q2);
			$solde->setAnnee_reference2($row->Annee_reference);
	}

	//r�cup�re toutes les entr�es de la table
	public function fetchAll2($str)
	{
		//r�cup�ration dans la variable $resultSet de toutes les entr�es de notre table
		$resultSet = $this->getDbTable()->fetchAll($str);

		//chaque entr�e est repr�sent�e par un objet Default_Model_Personne
		//qui est ajout� dans un tableau
		$entries = array();
		foreach($resultSet as $row)
		{
			$entry = new Default_Model_Solde();
			$entry->setId_personne($row->id_personne);
			$entry->setTotal_cp($row->total_cp);
			$entry->setTotal_q1($row->total_q1);
			$entry->setTotal_q2($row->total_q2);
			//$entry->setAnnee_reference($row->Annee_reference);
			$entry->setMapper($this);

			$entries[] = $entry;
		}

		return $entries;
	}

}

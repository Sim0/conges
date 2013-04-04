<?php
//"Default" est le namespace d�fini dans le bootstrap
class Default_Model_VacancesMapper
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
			$this->setDbTable('Default_Model_DbTable_Vacances');
		}
		return $this->_dbTable;
	}

	//sauve une nouvelle entr�e dans la table


	//r�cup�re une entr�e dans la table


	//r�cup�re toutes les entr�es de la table
	public function fetchAll($str)
	{
		//r�cup�ration dans la variable $resultSet de toutes les entr�es de notre table
		$resultSet = $this->getDbTable()->fetchAll($str);

		//chaque entr�e est repr�sent�e par un objet Default_Model_Vacances
		//qui est ajout� dans un tableau
		$entries = array();
		foreach($resultSet as $row)
		{
			$entry = new Default_Model_Vacances();
			$entry->setId($row->id);
			$entry->setZone($row->zone);
			$entry->setDate_debut($row->date_debut);
			$entry->setDate_fin($row->date_fin);
			$entry->setMapper($this);

			$entries[] = $entry;
		}

		return $entries;
	}

	public function jours_vacances( $debut_mois, $fin_mois)
	{
		return $this->getDbTable()->jours_vacances( $debut_mois, $fin_mois) ;
	}

}

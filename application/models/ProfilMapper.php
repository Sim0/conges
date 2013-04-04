<?php
//"Default" est le namespace d�fini dans le bootstrap
class Default_Model_ProfilMapper
{
	//$_dbTable va faire r�f�rence � un objet Zend_Db_Table_Abstract
	//dans notre cas la classe Default_Model_DbTable_Profil
	//du fichier application/models/DbTable/Profil.php
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
			$this->setDbTable('Default_Model_DbTable_Profil');
		}
		return $this->_dbTable;
	}

	//sauve une nouvelle entr�e dans la table
	public function save(Default_Model_Profil $profil)
	{
		//r�cup�ration dans un tableau des donn�es de l'objet $profil
		//les noms des cl�s du tableau correspondent aux noms des champs de la table
		$data = array(
               'login' => $profil->getLogin(),
               'mot_passe' => $profil->getMotDePasse()
		);

		//on v�rifie si un l'objet $profil contient un id
		//si ce n'est pas le cas, il s'agit d'un nouvel enregistrement
		//sinon, c'est une mise � jour d'une entr�e � effectuer
		if(null === ($id = $profil->getId()))
		{
			unset($data['id']);
			$this->getDbTable()->insert($data);
		}
		else
		{
			$this->getDbTable()->update($data, array('id = ?' => $id));
		}
	}

	//r�cup�re une entr�e dans la table
	public function find($id, Default_Model_Profil $profil)
	{
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return;
		}

		//initialisation de la variable $row avec l'entr�e r�cup�r�e
		$row = $result->current();

		//setting des valeurs dans notre objet $profil pass� en argument
		$profil->setId($row->id);
		$profil->setLogin($row->login);
		$profil->setMotDePasse($row->mot_passe);
	}

	//r�cup�re toutes les entr�es de la table
	public function fetchAll()
	{
		//r�cup�ration dans la variable $resultSet de toutes les entr�es de notre table
		$resultSet = $this->getDbTable()->fetchAll();

		//chaque entr�e est repr�sent�e par un objet Default_Model_Profil
		//qui est ajout� dans un tableau
		$entries = array();
		foreach($resultSet as $row)
		{
			$entry = new Default_Model_Profil();
			$entry->setId($row->id);
			$entry->setLogin($row->login);
			$entry->setMotDePasse($row->mot_passe);
			$entry->setMapper($this);

			$entries[] = $entry;
		}

		return $entries;
	}

	//permet de supprimer un utilisateur,
	//re�oit la condition de suppression (le plus souvent bas� sur l'id)
	public function delete($id)
	{
		$result = $this->getDbTable()->delete($id);
	}
}
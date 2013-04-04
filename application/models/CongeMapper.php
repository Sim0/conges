<?php
//"Default" est le namespace d�fini dans le bootstrap
class Default_Model_CongeMapper
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
			$this->setDbTable('Default_Model_DbTable_Conge');
		}
		return $this->_dbTable;
	}

	//sauve une nouvelle entr�e dans la table
	public function save(Default_Model_Conge $conge)
	{
		//r�cup�ration dans un tableau des donn�es de l'objet $conge
		//les noms des cl�s du tableau correspondent aux noms des champs de la table
		$data = array(
               	'id' => $conge->getId(),
               	'id_personne' => $conge->getId_personne(),
				'id_proposition'=> $conge->getId_proposition(),
               	'date_debut' => $conge->getDate_debut(),
               	'mi_debut_journee' => $conge-> getMi_debut_journee(),
			   	'date_fin' => $conge->getDate_fin(),
				'mi_fin_journee' => $conge->getMi_fin_journee(),
				'nombre_jours' => $conge->getNombre_jours(),
				'id_type_conge' => $conge->getId_type_conge(),
				'annee_reference'=> $conge->getAnnee_reference(),
				'ferme' => $conge->getFerme()		
		);

		//on v�rifie si un l'objet $conge contient un id
		//si ce n'est pas le cas, il s'agit d'un nouvel enregistrement
		//sinon, c'est une mise � jour d'une entr�e � effectuer
		if(null === ($id = $conge->getId()))
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
	public function find($id, Default_Model_Conge $conge)
	{
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return;
		}

		//initialisation de la variable $row avec l'entr�e r�cup�r�e
		$row = $result->current();

		//setting des valeurs dans notre objet $users pass� en argument
		$conge->setId($row->id);
		$conge->setId_personne($row->id_personne);
		$conge->setId_proposition($row->id_proposition);
		$conge->setDate_debut($row->date_debut);
		$conge->setMi_debut_journee($row->mi_debut_journee);
		$conge->setDate_fin($row->date_fin);
		$conge->setMi_fin_journee($row->mi_fin_journee);
		$conge->setNombre_jours($row->nombre_jours);
		$conge->setAnnee_reference($row->annee_reference);
		$conge->setId_type_conge($row->id_type_conge);
		$conge->setFerme($row->ferme);
	}

	//r�cup�re toutes les entr�es de la table
	public function fetchAll($str)
	{
		//r�cup�ration dans la variable $resultSet de toutes les entr�es de notre table
		$resultSet = $this->getDbTable()->fetchAll($str);

		//chaque entr�e est repr�sent�e par un objet Default_Model_Conge
		//qui est ajout� dans un tableau
		$entries = array();
		foreach($resultSet as $row)
		{
			$entry = new Default_Model_Conge();
			$entry->setId($row->id);
			$entry->setId_personne($row->id_personne);
			$entry->setId_proposition($row->id_proposition);
			$entry->setDate_debut($row->date_debut);
			$entry->setMi_debut_journee($row->mi_debut_journee);
			$entry->setDate_fin($row->date_fin);
			$entry->setMi_fin_journee($row->mi_fin_journee);
			$entry->setNombre_jours($row->nombre_jours);
			$entry->setId_type_conge($row->id_type_conge);
			$entry->setAnnee_reference($row->annee_reference);
			$entry->setFerme($row->ferme);
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
	public function somme($id,$annee_reference) 
    {
    	return $this->getDbTable()->somme($id,$annee_reference);
    }
    public function selctid() 
    {
    	return $this->getDbTable()->selctid();
    }
    
    public function somme_solde_annuel_confe($id_personnes, $debut_annee, $fin_annee)
    {
    	return $this->getDbTable()->somme_solde_annuel_confe($id_personnes, $debut_annee, $fin_annee);
    }
    
   	public function doublont( $debut_annee, $fin_annee)
    {
    	return $this->getDbTable()->doublont( $debut_annee, $fin_annee);
    }
    
    public function DateDebutMin($id,$debut_mois,$fin_mois)
    {
    	return $this->getDbTable()->DateDebutMin($id,$debut_mois,$fin_mois);
    }
    
    public function DateFinMax($id,$debut_mois,$fin_mois)
    {
    	return $this->getDbTable()->DateFinMax($id,$debut_mois,$fin_mois);
    }
    public function CongesNondoublont( $debut_mois,$fin_mois) 
	{
    	return $this->getDbTable()->CongesNondoublont( $debut_mois,$fin_mois) ;
    }
    
 	public function RecupererLeNombreConge( $id_personne,$date_debut)
	{
    	return $this->getDbTable()->RecupererLeNombreConge( $id_personne,$date_debut) ;
    }
     
 	public function DoublontAuNiveauPole($tableau_id,$debut_mois,$fin_mois)
	{
    	return $this->getDbTable()->DoublontAuNiveauPole($tableau_id,$debut_mois,$fin_mois);
    }
   
	public function  CongesNondoublontPole( $tableau_id,$debut_mois,$fin_mois) 
	{
    	return $this->getDbTable()-> CongesNondoublontPole( $tableau_id,$debut_mois,$fin_mois) ;
    }
}

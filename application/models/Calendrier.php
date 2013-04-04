<?php
//"Default" correspond au namespace que nous avons d�fini dans le bootstrap
class Default_Model_Calendrier
{
	//variables correspondant � chacun des champs de notre table users
	protected  $_nom;
	protected  $_prenom;
	protected  $_date_debut;
	protected  $_date_fin;
	
	
	//le mapper va nous fournir les m�thodes pour interagir avec notre table (objet de type Default_Model_PersonneMapper)
	protected $_mapper;

	//constructeur
	//le tableau d'options peut contenir les valeurs des champs � utiliser
	//pour l'initialisation de l'objet
	public function __construct(array $options = null)
	{
		if (is_array($options)) {
			$this->setOptions($options);
		}
	}

	//cette m�thode permet d'appeler n'importe quel settor en fonction
	//des arguments
	public function __set($name, $value)
	{
		$method = 'set' . $name;
		if (('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid guestbook property '.$name);
		}
		$this->$method($value);
	}

	//cette m�thode permet d'appeler n'importe quel gettor en fonction
	//du nom pass� en argument
	public function __get($name)
	{
		$method = 'get' . $name;
		if (('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid guestbook property '.$name);
		}
		return $this->$method();
	}

	//permet de g�rer un tableau d'options pass� en argument au constructeur
	//ce tabelau d'options peut contenir la valeur des champs � utiliser
	//pour l'initialisation de l'objet
	public function setOptions(array $options)
	{
		$methods = get_class_methods($this);
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (in_array($method, $methods)) {
				$this->$method($value);
			}
		}
		return $this;
	}


	public function setNom($nom)
	{
		$this->_nom = (string)$nom;
		return $this;
	}
	public function getNom()
	{
		return $this->_nom;
	}

	public function setPrenom($prenom)
	{
	
		$this->_prenom = (string) $prenom;
		return $this;

	}
	public function getPrenom()
	{
		return $this->_prenom;
	}


	public function setDate_debut($date_debut)
	{
		
		$this->_date_debut = $date_debut;
		return $this;
	}

	public function getDate_debut()
	{
		return $this->_date_debut;
	}

	public function setDate_fin($date_fin)
	{
		$this->_date_fin = $date_fin;
		return $this;
	}
	public function getDate_fin()
	{
		return $this->_date_fin;
	}
	
	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}
	public function getMapper()
	{
		//si la valeur $_mapper n'est pas initialis�e, on l'initialise (
		if(null == $this->_mapper){
			$this->setMapper(new Default_Model_CalendrierMapper());
		}

		return $this->_mapper;
	}

	//m�thodes de classe utilisant les m�thodes du mapper
	//cr�e ou met � jour une entr�e dans la table
	
	//r�cup�re une entr�e particuli�re
	public function obtenirClonnes() 
	{
		return  $this->getMapper()->obtenirClonnes ();
		
	}

	//r�cup�re toutes les entr�es de la table
	public function fetchAll($str)
	{
		return $this->getMapper()->fetchAll($str);
	}

}

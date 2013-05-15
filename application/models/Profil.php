<?php
//"Default" correspond au namespace que nous avons d�fini dans le bootstrap
class Default_Model_Profil
{
	//variables correspondant � chacun des champs de notre table users
	protected $_id;
	protected $_login;
	protected $_mot_passe;

	//le mapper va nous fournir les m�thodes pour interagir avec notre table (objet de type Default_Model_ProfilMapper)
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
			throw new Exception('Invalid guestbook property');
		}
		$this->$method($value);
	}

	//cette m�thode permet d'appeler n'importe quel gettor en fonction
	//du nom pass� en argument
	public function __get($name)
	{
		$method = 'get' . $name;
		if (('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid guestbook property');
		}
		return $this->$method();
	}

	//permet de g�rer un tableau d'options pass� en argument au constructeur
	//ce tableau d'options peut contenir la valeur des champs � utiliser
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

	//gettors and settors d'acc�s aux variables

	public function setId($id)
	{
		$this->_id = (int)$id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setLogin($login)
	{
		$this->_login = (string)$login;
		return $this;
	}
	public function getLogin()
	{
		return $this->_login;
	}

	public function setMotDePasse($mot_passe)
	{
		$this->_mot_passe = (string)$mot_passe;
		return $this;
	}
	public function getMotDePasse()
	{
		return $this->_mot_passe;
	}

	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}
	public function getMapper()
	{
		//si la valeur $_mapper n'est pas initialis�e, on l'initialise (
		if(null === $this->_mapper){
			$this->setMapper(new Default_Model_ProfilMapper());
		}

		return $this->_mapper;
	}


	//m�thodes de classe utilisant les m�thodes du mapper
	//cr�e ou met � jour une entr�e dans la table
	public function save()
	{
		$this->getMapper()->save($this);
	}

	//r�cup�re une entr�e particuli�re
	public function find($id)
	{
		$this->getMapper()->find($id, $this);
		return $this;
	}

	//r�cup�re toutes les entr�es de la table
	public function fetchAll()
	{
		return $this->getMapper()->fetchAll();
	}

	//permet la suppression
	public function delete($id)
	{
		$this->getMapper()->delete($id);
	}
}
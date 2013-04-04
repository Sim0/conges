<?php
class Default_Acl_MyAcl extends Zend_Acl
{
	//constructeur
	public function __construct()
	{
		$this->_initRessources();
		$this->_initRoles();
		$this->_initRights();

		//Zend_Registry permet de g�rer une collection de valeurs qui
		//sont peuvent �tre accessibles n'importe o� dans notre application
		//ont peut comparer son fonctionnement � une variable globale
		Zend_Registry::set('My_Acl', $this);
	}

	protected function _initRessources()
	{
		//cr�ation des ressources
		//une ressource correspond � un �l�ment pour lequel l'acc�s est contr�l�
		//ici, nous cr�ons une ressource par contr�leur, ce qui signifie
		//que nous allons contr�ler l'acc�s � nos contr�leurs
		//la m�thode addRessource() permet d'ajouter les ressources � l'ACL
		$this->addResource(new Zend_Acl_Resource('index'));
		$this->addResource(new Zend_Acl_Resource('error'));
		$this->addResource(new Zend_Acl_Resource('connexion'));
		$this->addResource(new Zend_Acl_Resource('login'));
		$this->addResource(new Zend_Acl_Resource('users'));
		$this->addResource(new Zend_Acl_Resource('test'));
	}

	protected function _initRoles()
	{
		//cr�ation des r�les
		//un r�le est un objet qui demande l'acc�s aux ressources
		//nous allons, ici, utiliser 3 r�les:
		//  - guest: compte invit� avec des droits limit�s
		//  - reader: simple acc�s en lecture
		//  - admin: acc�s total au site (lecture �criture
		$guest = new Zend_Acl_Role('guest');
		$equipe = new Zend_Acl_Role('equipe');
		$csm = new Zend_Acl_Role('csm');
		$admin = new Zend_Acl_Role('admin');

		//ajout des r�les � l'ACL avec la m�thode addRole()
		//le premier argument est le r�le � ajouter � l'ACL
		//le second argument permet d'indiquer l'h�ritage du groupe parent
		//reader va h�riter des droits de guest
		//admin va h�riter des droits de reader
		$this->addRole($guest);
		$this->addRole($equipe, $guest);
		$this->addRole($csm, $equipe);
		$this->addRole($admin, $csm);
	}

	protected function _initRights()
	{
		//d�finition des r�gles
		//la m�thode allow permet d'indiquer les permissions de chaque r�le
		//le premier argument permet de d�finir le r�l pour qui la r�gle est �crite
		//le second argument permet d'indiquer les contr�leurs
		//le troisi�me indique les actions du contr�leur
		//� noter qu'il aussi possible de refuser un acc�s gr�ce � la fonction deny()
		$this->allow('guest', array('index','error','connexion','login','test'));
		/*
		$this->allow('equipe', 'users', 'index');
		$this->allow('csm');
		$this->allow('admin', 'users');
		*/
		$this->allow('equipe');
		$this->allow('csm');
		$this->allow('admin');
	}
}
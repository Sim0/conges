<?php
//"Default" correspond au namespace que nous avons d�fini dans le bootstrap
class Default_Form_Login extends Zend_Form
{
	//l'initialisation et la configuration des �l�ments de notre formulaire
	//se trouveront dans le constructeur de la classe,
	//de cette fa�on tous les �l�ments de notre formulaire seront cr��s lors
	//de l'instaniation d'un objet de type Default_Form_Profil
	public function  __construct($options = null) {
		parent::__construct($options);

		//donne un nom � notre formulaire
		$this->setName('login');
		//$this->setMethod('post');
		//cr�ation d'un �l�ment input de type hidden







		//cr�ation d'un �l�ment de input de type text
		$profil = new Default_Model_Profil();
		$result_set_profils = $profil->fetchAll($str=array());
		$tableau_profils = array();
		foreach($result_set_profils as $p)
		{
			$tableau_profils [$p->getId()] = $p->getLogin();
		}

		$login= new Zend_Form_Element_Select('login');
		$login->addMultiOptions($tableau_profils);
		$login->setLabel('Profil : ');
		//indique que ce champs est requis et devra contenir une valeur
		$login->setRequired(true);
		//un filtre va effectuer des traitements sur la valeur de l'�l�ment concern�
		//StripTags a le m�me effet que la fonction PHP strip_tags(),
		//supprime les balises XHTML
		$login->addFilter('StripTags');
		//StringTrim a le m�me effet que la fonction PHP trim(),
		//supprime les espaces inutiles en d�but et fin de String
		$login->addFilter('StringTrim');
		//un validateur est une condition sur l'�l�ment qui si elle n'est pas respect�e,
		//annule le traitement
		//NotEmpty indique que le champs ne pourra pas �tre vide
		$login->addValidator('NotEmpty');



		//cr�ation d'un �l�ment input de type password
		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('Password: ');
		$password->setRequired(true);
		$password->addFilter('StripTags');
		$password->addFilter('StringTrim');
		$password->addValidator('NotEmpty');

		//cr�ation d'un �l�ment submit pour envoyer le formulaire
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Connect');
		$submit->style = array('float: right');

		//ajout des �l�ments au formulaire
		$this->addElements(array( $login, $password, $submit));
	}
}
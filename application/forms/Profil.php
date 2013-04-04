<?php
//"Default" correspond au namespace que nous avons d�fini dans le bootstrap
class Default_Form_Profil extends Zend_Form
{
	//l'initialisation et la configuration des �l�ments de notre formulaire
	//se trouveront dans le constructeur de la classe,
	//de cette fa�on tous les �l�ments de notre formulaire seront cr��s lors
	//de l'instaniation d'un objet de type Default_Form_Profil
	public function  __construct($options = null) {
		parent::__construct($options);

		//donne un nom � notre formulaire
		$this->setName('users');
		$this->removeDecorator('DtDdWrapper');
		$this->removeDecorator('HtmlTag');
		$this->removeDecorator('Label');
		//cr�ation d'un �l�ment input de type hidden
		$id = new Zend_Form_Element_Hidden('id');

		//cr�ation d'un �l�ment de input de type text
		$profil = new Default_Model_Profil();
		$result_set_profils = $profil->fetchAll($str=array());
		$tableau_profils = array();
		foreach($result_set_profils as $p)
		{
			$tableau_profils [$p->getId()] = $p->getLogin();
		}

		$id_profil= new Zend_Form_Element_Select('id_profil');
		$id_profil->addMultiOptions($tableau_profils);
		$id_profil->setRequired(true);
		$id_profil->addFilter('StripTags');
		$id_profil->addFilter('StringTrim');
		$id_profil->addValidator('NotEmpty');
		$id_profil->removeDecorator('DtDdWrapper');
		$id_profil->removeDecorator('HtmlTag');
		$id_profil->removeDecorator('Label');


		//cr�ation d'un �l�ment input de type password
		$password = new Zend_Form_Element_Password('password');
		$password->setRequired(true);
		$password->addFilter('StripTags');
		$password->addFilter('StringTrim');
		$password->addValidator('NotEmpty');
		$password->removeDecorator('DtDdWrapper');
		$password->removeDecorator('HtmlTag');
		$password->removeDecorator('Label');
		

		//cr�ation d'un �l�ment submit pour envoyer le formulaire
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->removeDecorator('DtDdWrapper');
		$submit->removeDecorator('HtmlTag');
		$submit->removeDecorator('Label');
		$this->addElements(array($id, $id_profil, $password, $submit));
	}
}
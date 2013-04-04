<?php
//"Default" correspond au namespace que nous avons d�fini dans le bootstrap
class Default_Form_Users extends Zend_Form
{
	//l'initialisation et la configuration des �l�ments de notre formulaire
	//se trouveront dans le constructeur de la classe,
	//de cette fa�on tous les �l�ments de notre formulaire seront cr��s lors
	//de l'instaniation d'un objet de type Default_Form_Users
	public function  __construct($options = null) {
		parent::__construct($options);

		//donne un nom � notre formulaire
		$this->setName('users');

		//cr�ation d'un �l�ment input de type hidden
		$id = new Zend_Form_Element_Hidden('id');

		//cr�ation d'un �l�ment de input de type text
		$firstname = new Zend_Form_Element_Text('firstname');
		//indique le label � utiliser pour l'�l�ment
		$firstname->setLabel('First name: ');
		//indique que ce champs est requis et devra contenir une valeur
		$firstname->setRequired(true);
		//un filtre va effectuer des traitements sur la valeur de l'�l�ment concern�
		//StripTags a le m�me effet que la fonction PHP strip_tags(),
		//supprime les balises XHTML
		$firstname->addFilter('StripTags');
		//StringTrim a le m�me effet que la fonction PHP trim(),
		//supprime les espaces inutiles en d�but et fin de String
		$firstname->addFilter('StringTrim');
		//un validateur est une condition sur l'�l�ment qui si elle n'est pas respect�e,
		//annule le traitement
		//NotEmpty indique que le champs ne pourra pas �tre vide
		$firstname->addValidator('NotEmpty');


		$lastname = new Zend_Form_Element_Text('lastname');
		$lastname->setLabel('Last name: ');
		$lastname->setRequired(true);
		$lastname->addFilter('StripTags');
		$lastname->addFilter('StringTrim');
		$lastname->addValidator('NotEmpty');

		$mail = new Zend_Form_Element_Text('mail');
		$mail->setLabel('Mail: ');
		$mail->setRequired(true);
		$mail->addFilter('StripTags');
		$mail->addFilter('StringTrim');
		$mail->addValidator('NotEmpty');
		//ce validateur v�rifie que la valeur de l'�l�ment correspond a une adresse mail
		$mail->addValidator('EmailAddress');

		//cr�ation d'un �l�ment input de type password
		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('Password: ');
		$password->setRequired(true);
		$password->addFilter('StripTags');
		$password->addFilter('StringTrim');
		$password->addValidator('NotEmpty');

		//cr�ation d'un �l�ment submit pour envoyer le formulaire
		$submit = new Zend_Form_Element_Submit('submit');
		//d�finit l'attribut "id" de l'�l�ment submit
		$submit->setAttrib('id', 'submitBt');

		//ajout des �l�ments au formulaire
		$this->addElements(array($id, $firstname, $lastname, $mail, $password, $submit));
	}
}
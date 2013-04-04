<?php
//"Default" correspond au namespace que nous avons d�fini dans le bootstrap
class Default_Form_Solde extends Zend_Form
{
	//l'initialisation et la configuration des �l�ments de notre formulaire
	//se trouveront dans le constructeur de la classe,
	//de cette fa�on tous les �l�ments de notre formulaire seront cr��s lors
	//de l'instaniation d'un objet de type Default_Form_Users
	public function  __construct($options = null) {
		parent::__construct($options);

		//donne un nom � notre formulaire
		$this->setName('solde');
		$this->removeDecorator('DtDdWrapper');
		$this->removeDecorator('HtmlTag');
		$this->removeDecorator('Label');
		//cr�ation d'un �l�ment input de type hidden
		$id = new Zend_Form_Element_Hidden('id');

		$date=date('D/d/m/Y');
		list($dcourt,$day, $month, $year) = explode("/", $date);
		$year =	(int)$year;
		
		$annee_reference_sl = new Zend_Form_Element_Text('annee_reference_sl');
		$annee_reference_sl->setRequired(true);
		$annee_reference_sl->addFilter('StripTags');
		$annee_reference_sl->addFilter('StringTrim');
		$annee_reference_sl->addValidator('NotEmpty');
		$annee_reference_sl->addValidator('StringLength',4);
		$annee_reference_sl->addValidator( new Zend_Validate_Between(array('min' => ($year-1),'max' => ($year+1))),  true);
		$annee_reference_sl->removeDecorator('DtDdWrapper');
		$annee_reference_sl->removeDecorator('HtmlTag');
		$annee_reference_sl->removeDecorator('Label');
		
		// bouton de creation
		$submit_sl = new Zend_Form_Element_Submit('submit_sl');
		$submit_sl->removeDecorator('DtDdWrapper');
		$submit_sl->removeDecorator('HtmlTag');
		$submit_sl->removeDecorator('Label');

		//ajout des �l�ments au formulaire
		$this->addElements(array(
		$id,
		$annee_reference_sl,
		$submit_sl));
	
	}
}

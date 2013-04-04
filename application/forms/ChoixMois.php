<?php
//"Default" correspond au namespace que nous avons d�fini dans le bootstrap
class Default_Form_ChoixMois extends Zend_Form
{
	//l'initialisation et la configuration des �l�ments de notre formulaire
	//se trouveront dans le constructeur de la classe,
	//de cette fa�on tous les �l�ments de notre formulaire seront cr��s lors
	
	public function  __construct($options = null) {
		parent::__construct($options);

		//donne un nom � notre formulaire
		$this->setName('choismois');

		//cr�ation de la liste muli-selection Mois auto_validation	
		
		$tableau_mois = array(0=>'choisir un mois',1=>'Janvier',2=>'Fevrier',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Aout',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Decembre');
		$num_mois= new Zend_Form_Element_Select('num_mois');
		$num_mois->addMultiOptions($tableau_mois);
		$num_mois->setOptions(array('onChange' => 'submit()'));
		
		
	
		$this->addElements(array(
		
		$num_mois));
	
	}
}
<?php
//"Default" correspond au namespace que nous avons d�fini dans le bootstrap
class Default_Form_ChoixAnnee extends Zend_Form
{
	//l'initialisation et la configuration des �l�ments de notre formulaire
	//se trouveront dans le constructeur de la classe,
	//de cette fa�on tous les �l�ments de notre formulaire seront cr��s lors
	
	public function  __construct($options = null) {
		parent::__construct($options);

		//donne un nom � notre formulaire
		$this->setName('choixannee');

		//cr�ation de la liste muli-selection Mois auto_validation	
		$date=date('D/d/m/Y');
		list($dcourt,$day, $m, $y) = explode("/", $date);
		
		
		$tableau_mois = array(0=>'choisir une annee',$y-1=>$y-1,$y=>$y,$y+1=>$y+1);
		$num_annee= new Zend_Form_Element_Select('num_annee');
		$num_annee->addMultiOptions($tableau_mois);
		$num_annee->setOptions(array('onChange' => 'submit()'));
		
		
	
		$this->addElements(array(
		
		$num_annee));
	
	}
}
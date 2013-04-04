<?php
//"Default" correspond au namespace que nous avons d�fini dans le bootstrap
class Default_Form_FiltreCsm extends Zend_Form
{
	//l'initialisation et la configuration des �l�ments de notre formulaire
	//se trouveront dans le constructeur de la classe,
	//de cette fa�on tous les �l�ments de notre formulaire seront cr��s lors
	
	public function  __construct($options = null) {
		parent::__construct($options);

		//donne un nom � notre formulaire
		$this->setName('filtrecsm');
		//cr�ation d'un �l�ment input de type hidden
		$id = new Zend_Form_Element_Hidden('id');

		//cr�ation de la liste muli-selection entites



		//cr�ation de la liste muli-selection Poles
		$pole = new Default_Model_Pole();
		$result_set_poles = $pole->fetchAll($str=array());
		$tableau_poles = array();
		foreach($result_set_poles as $p)
		{
			$tableau_poles [$p->getId()] = $p->getLibelle();
		}

		$id_pole= new Zend_Form_Element_Select('id_pole');
		$id_pole->addMultiOptions($tableau_poles);
		$id_pole->setLabel('Poles : ');
		
		//cr�ation de la liste muli-selection Fonctions
		$fonction = new Default_Model_Fonction();
		$result_set_fonctions = $fonction->fetchAll($str=array());
		$tableau_fonctions = array();
		foreach($result_set_fonctions as $p)
		{
			$tableau_fonctions [$p->getId()] = $p->getLibelle();
		}

		$id_fonction= new Zend_Form_Element_Select('id_fonction');
		$id_fonction->addMultiOptions($tableau_fonctions);
		$id_fonction->setLabel('Fonctions : ');



		//cr�ation d'un �l�ment submit pour envoyer le formulaire
		$submit = new Zend_Form_Element_Submit('submit');
		//d�finit l'attribut "id" de l'�l�ment submit
		$submit->setAttrib('id', 'submitBt');
		
		
		
		//ajout des �l�ments au formulaire
		$this->addElements(array(
		$id,
		$id_pole,
		$id_fonction,
		$submit));
	}
}

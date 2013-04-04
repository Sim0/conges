<?php
//"Default" correspond au namespace que nous avons d�fini dans le bootstrap
class Default_Form_Filtre extends Zend_Form
{
	//l'initialisation et la configuration des �l�ments de notre formulaire
	//se trouveront dans le constructeur de la classe,
	//de cette fa�on tous les �l�ments de notre formulaire seront cr��s lors
	
	public function  __construct($options = null) {
		parent::__construct($options);

		//donne un nom � notre formulaire
		$this->setName('calendrier');
		//cr�ation d'un �l�ment input de type hidden
		$id = new Zend_Form_Element_Hidden('id');

		//cr�ation de la liste muli-selection entites
		$entite = new Default_Model_Entite();
		$result_set_entites = $entite->fetchAll($str=array());
		$tableau_entites = array();
		$tableau_entites[0] ="";
		foreach($result_set_entites as $p)
		{
			$tableau_entites [$p->getId()] = $p->getLibelle();
		}

		$id_entite_ca= new Zend_Form_Element_Select('id_entite_ca');
		$id_entite_ca->addMultiOptions($tableau_entites);


		//cr�ation de la liste muli-selection Poles
		$pole = new Default_Model_Pole();
		$result_set_poles = $pole->fetchAll($str=array());
		$tableau_poles = array();
		$tableau_poles[0] ="";
		foreach($result_set_poles as $p)
		{
			
			$tableau_poles [$p->getId()] = $p->getLibelle();
		}

		$id_pole_ca= new Zend_Form_Element_Select('id_pole_ca');
		$id_pole_ca->addMultiOptions($tableau_poles);
		
		//cr�ation de la liste muli-selection Fonctions
		$fonction = new Default_Model_Fonction();
		$result_set_fonctions = $fonction->fetchAll($str=array());
		$tableau_fonctions = array();
		$tableau_fonctions[0] ="";
		foreach($result_set_fonctions as $p)
		{
			$tableau_fonctions [$p->getId()] = $p->getLibelle();
		}

		$id_fonction_ca= new Zend_Form_Element_Select('id_fonction_ca');
		$id_fonction_ca->addMultiOptions($tableau_fonctions);



		//cr�ation d'un �l�ment submit pour envoyer le formulaire
		$submit_ca = new Zend_Form_Element_Submit('submit_ca');
		//d�finit l'attribut "id" de l'�l�ment submit
		
		
		
		
		//ajout des �l�ments au formulaire
		$this->addElements(array(
		$id,
		$id_entite_ca,
		$id_pole_ca,
		$id_fonction_ca,
		$submit_ca));
	}
}

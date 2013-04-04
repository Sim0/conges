<?php
//"Default" correspond au namespace que nous avons d�fini dans le bootstrap
class Default_Form_Ferie extends Zend_Form
{
	//l'initialisation et la configuration des �l�ments de notre formulaire
	//se trouveront dans le constructeur de la classe,
	//de cette fa�on tous les �l�ments de notre formulaire seront cr��s lors
	
	public function  __construct($options = null) {
		parent::__construct($options);

		//donne un nom � notre formulaire
		$this->setName('ferie');
		$this->removeDecorator('DtDdWrapper');
		$this->removeDecorator('HtmlTag');
		$this->removeDecorator('Label');
	


		//cr�ation de la liste muli-selection Poles
		$date_debut_fr = new ZendX_JQuery_Form_Element_DatePicker('date_debut_fr');
		$date_debut_fr->setJQueryParam('dateFormat', 'yy-mm-dd');
		$date_debut_fr->setRequired(true);
		$date_debut_fr->removeDecorator('DtDdWrapper');
		$date_debut_fr->removeDecorator('HtmlTag');
		$date_debut_fr->removeDecorator('Label');
		
		$date=date('D/d/m/Y');
		list($dcourt,$day, $month, $year) = explode("/", $date);
		$year =	(int)$year;
		$annee_reference_fr = new Zend_Form_Element_Text('annee_reference_fr');
		$annee_reference_fr->setRequired(true);
		$annee_reference_fr->addFilter('StripTags');
		$annee_reference_fr->addFilter('StringTrim');
		$annee_reference_fr->addValidator('NotEmpty');
		$annee_reference_fr->addValidator('StringLength',4);
		$annee_reference_fr->addValidator( new Zend_Validate_Between(array('min' => ($year-1),'max' => ($year+1))),  true);
		$annee_reference_fr->removeDecorator('DtDdWrapper');
		$annee_reference_fr->removeDecorator('HtmlTag');
		$annee_reference_fr->removeDecorator('Label');   
	
		$tableau_mois = array(0=>'choisir un mois','fin du mois de ramadan'=>'fin du mois de ramadan','fete du sacrifice'=>'fete du sacrifice','Jour de lan de lhegire'=>'Jour de lan de lhegire','Naissance du prophete'=>'Naissance du prophete','Declarer jours feries civiles'=>'Declarer jours feries civiles');
		$num_fete_fr= new Zend_Form_Element_Select('num_fete_fr');
		$num_fete_fr->addMultiOptions($tableau_mois);
		$num_fete_fr->removeDecorator('DtDdWrapper');
		$num_fete_fr->removeDecorator('HtmlTag');
		$num_fete_fr->removeDecorator('Label');   

		//cr�ation d'un �l�ment submit pour envoyer le formulaire
		$submit_fr = new Zend_Form_Element_Submit('submit_fr');
		//d�finit l'attribut "id" de l'�l�ment submit
		$submit_fr->removeDecorator('DtDdWrapper');
		$submit_fr->removeDecorator('HtmlTag');
		$submit_fr->removeDecorator('Label');
		
		
		
		//ajout des �l�ments au formulaire
		$this->addElements(array(
		//$id_ferie_csm,
		$num_fete_fr,
		$date_debut_fr,
		$annee_reference_fr,
		
		$submit_fr));
	}
}

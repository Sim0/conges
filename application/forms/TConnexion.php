<?php
class Default_Form_TConnexion extends Default_Form_MyForm
{
	
	//l'initialisation et la configuration des �l�ments de notre formulaire
	//se trouveront dans le constructeur de la classe,
	//de cette fa�on tous les �l�ments de notre formulaire seront cr��s lors
	//de l'instaniation d'un objet de type Default_Form_Users
	

	public function init()
	{
		parent::init();
		
		// nom du form 
     	 $this->setName('Connexion');
     	 // Method par d�faut du form
         $this->setMethod('post');


        /*
         * Profil type select
         */ 
		   $iProfil = $this->createElement('select', 'Profil', array(   'label' => 'Profil ',
			  															'name' => 'Profil',
		                                                                'required'   => 'true',
																    ) 
	          							   ); 
		/*
         * Password type select
         */ 				   

		  $iPassword = new Zend_Form_Element_Password('Password');
		  $iPassword->setRequired(true);							   
	      $iPassword->setLabel('Mot de passe ');	
	      $iPassword->addDecorator('Fpassinput', array('label')); 			   
		  $iPassword->setAttrib('placeholder','Saisir votre mot de passe ...'); 		   
	      $iPassword->setErrorMessages(array("Mot de passe invalide !"));    							   
	
		
	     /*
         * Debut midi type checkbox 
         */

		 $iRemember =   new Zend_Form_Element_Checkbox( 'Remember',array('label' => 'se souvenir de moi ?'));
	      
	      
	      
	      
		/*
         * Submit type button 
         * 
         */
		   $iSubmit =   new Zend_Form_Element_submit('Connexion');

		
		   
		   
		//ajout des �l�ments au formulaire
		$this->addElements(array(
								 $iProfil,
								 $iPassword,
								 $iRemember,
								 $iSubmit
								 )
						   );   
		
	}
	
	

}
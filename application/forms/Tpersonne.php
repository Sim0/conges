<?php
#region MBA
// application/forms/TPersonne.php
 
class Default_Form_TPersonne extends Zend_Form
{
	public function init()
	{
	 	// nom du formulaire 
		$this->setName('createPersonne');
	   
     	// La m�thode HTTP d'envoi du formulaire
   		 $this->setMethod('post');
   		  $decorators = array('label','ViewHelper','Errors','description','htmltag','DtDdWrapper');
   		 foreach ($decorators as $decorator)
   		 { $this->removeDecorator($decorator);}
   		 
   		
   		 // le chemin du d�corateur est d�fini.
           $this->addElementPrefixPath('My_Form_Decorators',
                       APPLICATION_PATH.'../../My/Form/Decorators',
		              'decorator');
   		 /*
   		  * Test decorator ftextinput
   		  * 
   		  */
   		
   		 
		 /*
         * Champ input type text nom 
         * Validation : requis,
         * Filtre : StringTrim (supprime les espaces en d�but et fin ),
         *          StringTags (supprime les balises html et php)
         */
   		 $this->addElement('text','Nom',array(
   		 'label' => 'Nom',
   		 'placeholder' => 'Entrez nom�',
   		 'required' => true,
   		 'description' => 'required',
   		 'decorators' => array(
            'Ftextinput', array()),
   		 
   		 
   		 ));
   		 
        
        /*
         * Champ input type text Prenom 
         * Validation : requis,
         * Filtre : StringTrim (supprime les espaces en d�but et fin ),
         *          StringTags (supprime les balises html et php)
         */
        $this->addElement('text', 'Prenom', array(
            'label'      => 'Prenom',
            'required'   => true,
            'placeholder' => 'Entrez prenom�',
            'filters'    => array('StringTrim','StripTags'),
            'validators' => array(
               
            ),
            'description' =>'description ici..',
            'decorators' => array(
            'Ftextinput', array()),
            
           
        ));
        /*
         * Date entr�e type jquery_x datepicker
         * 
         */
        
		$date_entree_pr = new ZendX_JQuery_Form_Element_DatePicker('date_entree');
		$date_entree_pr->setJQueryParam('dateFormat', 'yy-mm-dd');
		$date_entree_pr->setLabel("Date d'entree");
		$date_entree_pr->setRequired(true);
		$date_entree_pr->addDecorator('Ftextinput', array('label'));
		
        
		$this->addElement($date_entree_pr);
		
		/*
         * Date debut type jquery_x datepicker
         * 
         */
		
		$date_debut = new ZendX_JQuery_Form_Element_DatePicker('date_debut');
		$date_debut->setJQueryParam('dateFormat', 'yy-mm-dd');
		$date_debut->setLabel("Date debut");
		$date_debut->setRequired(true);
		$date_debut->addDecorator('Ftextinput', array('label'));
		
		
		
        
		$this->addElement($date_debut);
		    
		
        
		
		/*
         * Liste d�roulante "P�le" peupl�e � partir de la BD via setDbOptions
         */
		
		
		$this->addElement('select','pole',array(
		    'label'  => 'Pole :',
		    'required' => true,
		    ));
                            
		
		
		/*
         * Liste d�roulante "Fonctions" peupl�e � partir de la BD via setDbOptions
         */
		 
		$this->addElement('select', 'fonctions' ,array(
		    'label'  => 'Fonctions :',
		     'required' => true,
		   
		    
		 ));
		 
		 /*
         * Champ type text 
         * Validation : requis,NotEmpty,Regex (entre 0 et 100)
         * Filtre : StringTrim (supprime les espaces en d�but et fin ),
         *          StringTags (supprime les balises html et php)
         */
		 
		 $this->addElement('text','pourcentage',array(
		 'label' => 'Pourcentage',
		 'value' => '100',
		 'required' => true,
		 'filters' => array('StringTrim','StripTags'),
		 'description' => 'description : entre 0 et 100',
		 'validators' => array(  array('Regex', 
				                        true,
				                        array('pattern'=> '/^[1-9]?[0-9]{1}$|^100$/',
				                        'messages' => array(
                                        'regexNotMatch'=>'Pourcentage : Seulement valeurs entre 0 et 1 accept�es'
                               ))),
		                       ),
		 'decorators' => array(
            'Ftextinput', array()),
		 
		  ));
		
		  
		  $this->addElement('checkbox','Stage',array(
		  'label' => 'stage ?',
		  
		  ));
		  
		  $this->addElement('submit', 'creer',
		   array('label' => 'valider'));
      
    
	}
	
    /*
     * function return : zend_element_select($elementNam) peupl� | null
     * function params :
     * &$objet : la r�f�rence du type de l'objet dont on veut r�cup�rer la liste depuis la BD 
     * 
     * $id_function : le nom de la fonction qui retourne le Id de l'objet, par d�faut getId sinon il faut
     * renseign� le nom de la fonction. [optionnel]
     * 
     * $libelle_function : Par d�faut getLibelle() sinon renseignez nom de m�thode. [optionnel]
     * 
     * $str[] : tableau dans lequel seront r�cup�r�s les objets depuis la base. [optionnel]
     * 
     * desc : fonction qui sert � peupler un element du formulaire select � partir d'une liste d'objet
     * r�cup�r�e de la base de donn�e. la fonction s'applique sur "tout" type d'objet.
     */
	public function setDbOptions($elementName, &$object, $id_function ='getId', $libelle_function = 'getLibelle', $str = array())
				{
					// v�rifie si le champ en question est un "select"
					if($this->getElement($elementName) instanceof Zend_Form_Element_Select)
					{
						// check si l'objet � la m�thode getMapper (pour la liaison avec la base)
						if(method_exists($object, "getMapper"))
						{ 
						  $mapper = $object->getMapper();
						// check la m�thode fetchAll() existe
						  if(method_exists($mapper, "fetchAll"))
							{ 
							   $str = $object->getMapper()->fetchAll($str);
						    }
							$objArray = array();
						    foreach($str as $p)
							{
								/* remplie le tableau objArray avec les id et libelle 
								 * obtenues via les fonction variable $id_function et $libelle_function
								*/
								$objArray [$p->$id_function()] = $p->$libelle_function();
							}

			  	return $this->getElement($elementName)->setOptions(array('MultiOptions' =>  $objArray));
					}	
					return; }
				return ;			}
				

		/*
		 * function return : $valid boolean selon la validit� du formulaire.
		 * @param :
		 * $data : valeurs entr�es dans le formulaire.
		 * 
		 * desc: fonction qui sert � detecter les champ de saisie erron�s et les entourez en rouge.
		 * 
		 */		
				
		 public function isValid($data)
		    {
		        $valid = parent::isValid($data);
		  //Boucle sur les �l�ments du formulaire point�  (par $this)
		        foreach ($this->getElements() as $element) {
                // en cas d'erreur sur une saisie
		        	if ($element->hasErrors()) {
		        		/*On r�cup�re l'attribut class de l'element pour le concatener avec 'error' ou lui affecter 'error'
		        		* pour l'entourer ensuite en utilisant css
		        		*/
		                $ancClass = $element->getAttrib('class');
		                if (!empty($ancClass)) {
		                    $element->setAttrib('class', $ancClass . ' error');
		                } else {
		                    $element->setAttrib('class', 'error');
		                }
		            }
		        }
		 
		        return $valid;
		    }
	
}
#endregion MBA
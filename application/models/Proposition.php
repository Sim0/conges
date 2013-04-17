<?php
//"Default" correspond au namespace que nous avons d�fini dans le bootstrap
class Default_Model_Proposition
{
	//variables correspondant � chacun des champs de notre table proposition
	protected  $_id;
	protected  $_id_personne;
	protected  $_date_debut;
	protected  $_mi_debut_journee;
	protected  $_date_fin;
	protected  $_mi_fin_journee;
	protected  $_nombre_jours;
	protected  $_etat;

	//le mapper va nous fournir les m�thodes pour interagir avec notre table (objet de type Default_Model_PropositionMapper)
	protected $_mapper;

	//constructeur
	//le tableau d'options peut contenir les valeurs des champs � utiliser
	//pour l'initialisation de l'objet
	public function __construct(array $options = null)
	{
		if (is_array($options)) {
			$this->setOptions($options);
		}
	}

	//cette m�thode permet d'appeler n'importe quel settor en fonction
	//des arguments
	public function __set($name, $value)
	{
		$method = 'set' . $name;
		if (('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid guestbook property '.$name);
		}
		$this->$method($value);
	}

	//cette m�thode permet d'appeler n'importe quel gettor en fonction
	//du nom pass� en argument
	public function __get($name)
	{
		$method = 'get' . $name;
		if (('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid guestbook property '.$name);
		}
		return $this->$method();
	}

	//permet de g�rer un tableau d'options pass� en argument au constructeur
	//ce tabelau d'options peut contenir la valeur des champs � utiliser
	//pour l'initialisation de l'objet
	public function setOptions(array $options)
	{
		$methods = get_class_methods($this);
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (in_array($method, $methods)) {
				$this->$method($value);
			}
		}
		return $this;
	}

	//gettors and settors d'acc�s aux variables
	public function setId($id)
	{
		$this->_id = (int)$id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setId_personne($id_personne)
	{
		$this->_id_personne = (int)$id_personne;
		return $this;
	}
	public function getId_personne()
	{
		return $this->_id_personne;
	}

	public function setDate_debut($date_debut)
	{
		/*if (!($date_debut instanceof Zend_Date))
		{
			throw new Exception('Valeur date de debut non valide');
		}*/
		$this->_date_debut = $date_debut;
		return $this;

	}
	public function getDate_debut()
	{
		return $this->_date_debut;
	}

	public function setMi_debut_journee($mi_debut_journee)
	{
		$this->_mi_debut_journee = $mi_debut_journee;
		return $this;
	}
	public function getMi_debut_journee()
	{
		return $this->_mi_debut_journee;
	}

	public function setDate_fin($date_fin)
	{
		/*if (!($date_fin instanceof Zend_Date))
		{
			throw new Exception('Valeur date de fin  non valide');
		}*/
		$this->_date_fin = $date_fin;
		return $this;
	}

	public function getDate_fin()
	{
		return $this->_date_fin;
	}

	public function setMi_fin_journee($mi_fin_journee)
	{
		$this->_mi_fin_journee = $mi_fin_journee;
		return $this;
	}
	public function getMi_fin_journee()
	{
		return $this->_mi_fin_journee;
	}
	public function setNombre_jours()
	{
		$this->_nombre_jours =(float)$this->calculNombreDuJours();
		return $this;
	}
	public function  getNombre_jours()
	{
		return $this->_nombre_jours;
	}

	public function setEtat($etat)
	{
		$this->_etat = (string)$etat;
		return $this;
	}

	public function  getEtat()
	{
		return $this->_etat;
	}



	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}
	public function getMapper()
	{
		//si la valeur $_mapper n'est pas initialis�e, on l'initialise (
		if(null == $this->_mapper){
			$this->setMapper(new Default_Model_PropositionMapper());
		}

		return $this->_mapper;
	}

	//m�thodes de classe utilisant les m�thodes du mapper
	//cr�e ou met � jour une entr�e dans la table
	public function save()
	{
		$this->getMapper()->save($this);
	}

	//r�cup�re une entr�e particuli�re
	public function find($id)
	{
		$this->getMapper()->find($id, $this);
		return $this;
	}

	//r�cup�re toutes les entr�es de la table
	
		public function fetchAll($str)
	{
		return $this->getMapper()->fetchAll($str);
	}
	
	
	//permet la suppression
	public function delete($id)
	{
		$this->getMapper()->delete($id);
	}

	
	
	/*
	 * retourne le nombre de jours ouveres entre la date du debut de conge et la date du fin de conge
	 * */

    /* MTA : Mohamed khalil Takafi */		
     public function calculNombreDuJours()
	{
	
		// tu peut utiliser cette fonction pour afficher les nombre totale ouvere pour un mois donn�
		$date_depart = $this->getDate_debut();    
    	$date_fin1 = $this->getDate_fin();
    	$date_debut = strtotime($date_depart );   // date de debut 
    	$date_fin = strtotime($date_fin1 );       // date de debut 
    	$tableau_jours_feries = array(); 
	    $annee = (int)date('Y', $date_debut);     // ann�e 
	    $feris = new Default_Model_Ferie();       
	    $tableau_jours_feries = $feris->RecupererLesJoursFeries($annee); // recup�rer les jours f�ri�s relative � l'ann�e en cours 
		$nb= count($tableau_jours_feries );  	  // taille du tableau 
		$tableau = array();                 
		
		for ($i=0;$i<$nb;$i++)    // remplir le tableau avec les jours f�ri�s 
		{
			$tableau[$i]=$tableau_jours_feries[$i]['date_debut'];	
		}
     //var_dump($tableau);
	 $nb_jours_ouvres = 0;		 
    // Mettre <= si on souhaite prendre en compte le dernier jour dans le d�compte
    
	 while ($date_debut <= $date_fin)  
    {   
         
    	// Si le jour suivant n'est ni un dimanche (0) ou un samedi (6), ni un jour f�ri�, on incr�mente les jours ouvr�s
	    if (!in_array(date('w', $date_debut), array(0, 6)) && !in_array(date(date('Y', $date_debut).'-m-d', $date_debut),$tableau) )    // MTA : date('Y', $date_debut).'-m-d', $date_debut)
	    {
	    		$nb_jours_ouvres++;
	    }
	    	$date_debut = mktime(date('H', $date_debut), date('i', $date_debut), date('s', $date_debut), date('m', $date_debut), date('d', $date_debut)+1, date('Y', $date_debut));
    	 	
    }
            
      
    
     //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   	                          // si ( D_journ�e = 1  || F_journ�e = 1 )    et     Date_debut == Date_fin 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
        if ((($this->getMi_debut_journee() == True) ||($this->getMi_fin_journee() == True)) && ($this->getDate_debut() == $this->getDate_fin()) )
		{   
		    $nb_jours_ouvres = 1;
			
            // date_debut <> weekend   ou    date_debut <> f�ri� 
		   if ((in_array(date('w', $date_debut - 1), array(0, 6)) || in_array(date(date('Y', $date_debut - 1).'-m-d', $date_debut - 1), $tableau))) 	
		   		return   $nb_jours_ouvres - 1;
		   else
		   		return   $nb_jours_ouvres - 0.5; 

		}

       //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                          // si ( D_journ�e = 1  &&  F_journ�e = 1 )    et     Date_debut <> Date_fin 
	   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		elseif ((($this->getMi_debut_journee() == True) && ($this->getMi_fin_journee() == True))  && ($this->getDate_debut() != $this->getDate_fin()) )
		{  
		
		    // date_debut = weekend  ou date_debut=f�ri�     et   date_fin = weekend  ou date_fin=f�ri�
			if ((in_array(date('w', $date_debut - 1), array(0, 6)) || in_array(date(date('Y', $date_debut - 1).'-m-d', $date_debut - 1), $tableau)) && (in_array(date('w', $date_fin), array(0, 6)) || in_array(date(date('Y', $date_fin).'-m-d', $date_fin), $tableau)))
			{	  
			      return $nb_jours_ouvres; // MTA
			      
			}
	        // date_debut <> weekend  ou date_debut <> f�ri�    et   date_fin <> weekend  <> date_fin=f�ri�	
			elseif((!in_array(date('w', $date_debut - 1), array(0, 6)) || !in_array(date(date('Y', $date_debut - 1).'-m-d', $date_debut - 1), $tableau)) && (!in_array(date('w', $date_fin), array(0, 6)) || !in_array(date(date('Y', $date_fin).'-m-d', $date_fin), $tableau)))
			{ 	 
				 return $nb_jours_ouvres - 1; // MTA
			}
             // date_debut = week    ou    date_debut = f�ri�     ou  date_fin = week    ou    date_fin = f�ri�  
			elseif(in_array(date('w', $date_debut - 1), array(0, 6)) || in_array(date(date('Y', $date_debut - 1).'-m-d', $date_debut - 1), $tableau) || (in_array(date('w', $date_fin), array(0, 6)) || in_array(date(date('Y', $date_fin).'-m-d',$date_fin), $tableau)))
            {   
				  
                	return $nb_jours_ouvres - 0.5; 
            }

		}

		 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	                             //  si ( D_journ�e = 1  || F_journ�e = 1 )    et     Date_debut <> Date_fin 
		 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		elseif ((($this->getMi_debut_journee() == True) || ($this->getMi_fin_journee() == True))  && ($this->getDate_debut() !=$this->getDate_fin()) )
		{	
				
		    //(1)// (date_debut == weekend   ou   date_debut == feri�)     et   (date_fin == weekend    ou   date_fin == feri�)
			if((in_array(date('w', $date_debut - 1), array(0, 6)) || in_array(date(date('Y', $date_debut - 1).'-m-d', $date_debut - 1), $tableau)) && (in_array(date('w', $date_fin), array(0, 6)) || in_array(date(date('Y', $date_fin).'-m-d', $date_fin), $tableau))) 
			    return $nb_jours_ouvres ; 
			    
		    //(2)// (date_debut <> weekend et date_debut <> feri�)    et   (date_fin <> weekend et date_fin <> feri�)
		    elseif((!in_array(date('w', $date_debut - 1), array(0, 6)) || !in_array(date(date('Y', $date_debut - 1).'-m-d', $date_debut - 1), $tableau)) && (!in_array(date('w', $date_fin), array(0, 6)) || !in_array(date(date('Y', $date_fin).'-m-d', $date_fin), $tableau))) 
		    {
		    	return $nb_jours_ouvres -0.5 ; 
		    }
		    	
		    	//(3)//(date_debut ==  weekend ou date_debut ==  feri�)		
		    elseif(in_array(date('w', $date_debut - 1), array(0, 6)) || in_array(date(date('Y', $date_debut - 1).'-m-d', $date_debut - 1), $tableau))
		    {
		          	if($this->getMi_debut_journee() == True)   // debut_midi = true 
						return $nb_jours_ouvres;
					else                                  
						return $nb_jours_ouvres  - 0.5;      // debut_midi = false 
		    	
		    }
		    	//(3)//(date_fin ==  weekend ou date_fin ==  feri�)		
		    elseif(in_array(date('w', $date_fin), array(0, 6)) || in_array(date(date('Y',  $date_fin).'-m-d',  $date_fin), $tableau))
		    {
		          	if($this->getMi_fin_journee() == True)   // fin_midi = true 
						return $nb_jours_ouvres;
					else                                  
						return $nb_jours_ouvres  - 0.5;      // fin_midi = false 
		    	
		    }
		    	
		
	    }
	   return $nb_jours_ouvres;

	}
	



	public function joursOuvresDuMois($debut_mois,$fin_mois)
	{

		// tu peut utiliser cette fonction pour afficher les nombre totale ouvere pour un mois donn�
    	$date_debut = strtotime($debut_mois);
    	$date_fin = strtotime($fin_mois);

    	$tableau_jours_feries = array(); // Tableau des jours feri�s
	    $annee = (int)date('Y', $date_debut);
	    $feris = new Default_Model_Ferie();
	    $tableau_jours_feries = $feris->RecupererLesJoursFeries($annee);
		$nb= count($tableau_jours_feries );
		$tableau = array();
	
		for ($i=0;$i<$nb;$i++)
		{
			$tableau[$i]=$tableau_jours_feries[$i]['date_debut'];
			
		}
   		 $nb_jours_ouvres = 0;
    // Mettre <= si on souhaite prendre en compte le dernier jour dans le d�compte
    while ($date_debut <= $date_fin) 
    {
    // Si le jour suivant n'est ni un dimanche (0) ou un samedi (6), ni un jour f�ri�, on incr�mente les jours ouvr�s
	    if (!in_array(date('w', $date_debut), array(0, 6)) && !in_array(date(date('Y', $date_debut).'-n-j', $date_debut),$tableau)) 
	    {
	    	$nb_jours_ouvres ++;
	    }
	    	$date_debut = mktime(date('H', $date_debut), date('i', $date_debut), date('s', $date_debut), date('m', $date_debut), date('d', $date_debut) + 1, date('Y', $date_debut));
	}
		return $nb_jours_ouvres;
	}

}

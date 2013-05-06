<?php
class FerieController extends Zend_Controller_Action
{

     public function preDispatch() /* MTA : Mohamed khalil Takafi */
    {
    	    $doctypeHelper = new Zend_View_Helper_Doctype();
            $doctypeHelper->doctype('HTML5');
    		$this->_helper->layout->setLayout('mylayout');
	}
	
	
	
	public function indexAction()
	{
		
		$ferie = new Default_Model_Ferie;
		$paginator = Zend_Paginator::factory($ferie->fetchAll($str=array()));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->ferieArray = $paginator;
		$ferie = new Default_Model_Ferie();
		$paginator = Zend_Paginator::factory($ferie->fetchAll($str=array()));
		$paginator->setItemCountPerPage(15);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->ferieArray = $paginator;
	}

	public function createAction()
	{
		//cr�ation du fomulaire
		$form = new Default_Form_Ferie();
		//indique l'action qui va traiter le formulaire
		$form->setAction($this->view->url(array('controller' => 'ferie', 'action' => 'create'), 'default', true));
		$form->submit_fr->setLabel('Declarer');
		$data = array();
		//assigne le formulaire � la vue
		$this->view->form = $form;
		$this->view->title = "Declaration des jours feries CSM";
		//si la page est POST�e = formulaire envoy�
		if($this->_request->isPost())
		{
			$data = $this->_request->getPost();

			if($form->isValid($data))
			{
				
				
					$type_civil = '1';
					$type_religieu = '2';
					$ferie = new Default_Model_Ferie();
					
					
				if ($form->getValue('num_fete_fr')=='Declarer jours feries civiles')
				{
					$annee =$form->getValue('annee_reference_fr');
					$ferie1 = $ferie->fetchAll('id_type ='.$type_civil.'&&'. 'annee_reference ='.$annee);
					if (count($ferie1)==0)
					{
						$table = $this->_helper->validation->jours_feries_csm($annee);
					
						for ($i = 0; $i <= 8; $i++) 
			 			{
			 				$ferie->setId_type($type_civil);
							$ferie->setDate_debut($table[0][$i]);
							$ferie->setAnnee_reference($form->getValue('annee_reference_fr'));
							$ferie->setLibelle($table[1][$i]);
							$ferie->save();
			 			}
			 			$this->_helper->redirector('index');
					}
					else 
					{
						echo "Pour l'annee ".$form->getValue('annee_reference_fr')." les fetes nationaux sont deja declarer";
						$form->populate($data);
					}
				}
				elseif($form->getValue('num_fete_fr')!='Declarer jours feries civiles')
				{
					
					$ferie2 = new Default_Model_Ferie();
					$ferie2 = $ferie2->ChercheUnJourFerie( $form->getValue('num_fete_fr'),$form->getValue('annee_reference_fr'));
					if (count($ferie2)==0)
					{
						$ferie->setId_type($type_religieu);
						$ferie->setDate_debut($form->getValue('date_debut_fr'),'yy-mm-dd');
						$ferie->setAnnee_reference($form->getValue('annee_reference_fr'));
						$ferie->setLibelle($form->getValue('num_fete_fr'));
						$ferie->save();
						$this->_helper->redirector('index');
					}
					
					else 
					{
						echo "Pour l'annee ".$form->getValue('annee_reference_fr')." la fete de :".$form->getValue('num_fete_fr')." est deja declarer";
						$form->populate($data);
					}
				}
			}	
			else 
			{
				$form->populate($data);
				
			}
		}
		else
		{
			$form->populate($data);
		}
	}
	

	public function editAction()
	{
		//cr�ation du fomulaire
		$form = new Default_Form_Ferie();
		//indique l'action qui va traiter le formulaire
		$form->setAction($this->view->url(array('controller' => 'ferie', 'action' => 'edit'), 'default', true));
		$form->submit->setLabel('Modifier');

		//assigne le formulaire � la vue
		$this->view->form = $form;

		//si la page est POST�e = formulaire envoy�
		if($this->getRequest()->isPost())
		{
			//r�cup�ration des donn�es envoy�es par le formulaire
			$data = $this->getRequest()->getPost();

			//v�rifie que les donn�es r�pondent aux conditions des validateurs
			if($form->isValid($data))
			{
				
				//cr�ation et initialisation d'un objet Default_Model_Ferie
				//qui sera enregistr� dans la base de donn�es
				$ferie = new Default_Model_Ferie();
				$ferie->setId($form->getValue('id'));
				$ferie->setId_personne($form->getValue('id_personne'));
				
				//$date_debut = new Zend_Date;
				//$date_debut->set($form->getValue('date_debut_fr'),'yy-mm-dd');
				$ferie->setDate_debut($form->getValue('date_debut_fr'),'yy-mm-dd');
				$ferie->setDate_fin($form->getValue('date_fin'),'yy-mm-dd');
				$ferie->setMi_debut_journee($form->getValue('mi_debut_journee_fr'));
				$ferie->setMi_fin_journee($form->getValue('mi_fin_journee'));
				$ferie->setNombre_jours();
				//$ferie->setAnnee_reference($form->getValue('annee_reference_fr'));
				$ferie->setEtat('NC');
					// regarde le probleme de l'anne de reference
					
				
			
				if($this->_helper->validation->verifierConges($form->getValue('id_personne'),$form->getValue('date_debut_fr'),$form->getValue('date_fin'),$form->getValue('mi_debut_journee_fr'),$form->getValue('mi_fin_journee'))&& $this->_helper->validation->verifierFeries($form->getValue('id_personne'),$form->getValue('date_debut_fr'),$form->getValue('date_fin'),$form->getValue('mi_debut_journee_fr'),$form->getValue('mi_fin_journee')))
				{
					$ferie->save();
					//redirection
					$this->_helper->redirector('index');
				}
				else 
				{
					$form->populate($data);
					echo "<strong><em><span style='background-color:rgb(255,0,0)'> ferie ou conge deja demande</span></em></strong>";
				}
			}
			else
			{
				//si erreur rencontr�e, le formulaire est rempli avec les donn�es
				//envoy�es pr�c�demment
				$form->populate($data);
			}
		}
		else
		{
			//r�cup�ration de l'id pass� en param�tre
			$id = $this->_getParam('id', 0);

			if($id > 0)
			{
				//r�cup�ration de l'entr�e
				$ferie = new Default_Model_Ferie();
				$ferie = $ferie->find($id);

				//assignation des valeurs de l'entr�e dans un tableau
				//tableau utilis� pour la m�thode populate() qui va remplir le champs du formulaire
				//avec les valeurs du tableau
				$data[] = array();
				$data['id'] = $ferie->getId();
				$data['id_personne'] = $ferie->getId_personne();
				$data['date_debut'] = $ferie->getDate_debut();
				$data['mi_debut_journee'] = $ferie->getMi_debut_journee();
				$data['date_fin'] = $ferie->getDate_fin();
				$data['mi_fin_journee'] = $ferie->getMi_fin_journee();
				$data['nombre_jours'] = $ferie->getNombre_jours();
			//	$data['nombre_jours'] = $ferie->getAnnee_reference();
				$data['etat'] = $ferie->getEtat();
				
				$form->populate($data);
			
				
				
			}
		}
	}

	public function deleteAction()
	{
		//r�cup�re les param�tres de la requ�te
		$params = $this->getRequest()->getParams();

	

		//v�rifie que le param�tre id existe
		if(isset($params['id']))
		{
			$id = $params['id'];

			//cr�ation du mod�le pour la suppression
			$ferie = new Default_Model_Ferie();
			//appel de la fcontion de suppression avec en argument,
			//la clause where qui sera appliqu�e
			$result = $ferie->delete("id=$id");

			//redirection
			$this->_helper->redirector('index');
		}
		else
		{
			$this->view->form = 'Impossible delete: id missing !';
		}
	}
		/*
		 * cette fonction permet � l'admin de valiser les feries et les enregistr� dans 
		 * la table :conge
		 */
	
	public function accepterAction()
	{
		//r�cup�re les param�tres de la requ�te
		$params = $this->getRequest()->getParams();
		//v�rifie que le param�tre id existe
		
		
		if(isset($params['id']))
		{
			$id = $params['id'];
			$ferie = new Default_Model_Ferie();
			$result = $ferie->find($id);
			$nombre_jours = $result->getNombre_jours();
			$date=date('Y-m-d');
			list($annee_debut, $mois_debut,$jour_debut ) = explode("-", $date);
			list($annee_fin, $mois_fin,$jour_fin ) = explode("-", $date);
			$debut_annee_reference = $annee_debut.'-01-01';
			$fin_annee_reference = $annee_debut.'-12-31';
			if ($this->_helper->validation->verifierSolde ($id,$debut_annee_reference, $fin_annee_reference,$annee_debut,$nombre_jours))
			{
				echo " le solde est epuise";
				//$this->_helper->redirector('message');
			}
			
			$result->setEtat("OK")->save();
			
			
			
			// apres la validation de la ferie sera enregister dans la table conge
			
			
			
			
			$str=NULL;
			$conge = new Default_Model_Conge();
			$resultat_id_conge = $conge->fetchAll($str);
			$tableau_id_conge = array();
			$index =0;
			foreach($resultat_id_conge as $c)
			{
				$tableau_id_conge[$index] = $c->getId_ferie();
				$index++;
			}
			$conge->setId_ferie($result->getId('id'));
			$conge->setId_personne($result->getId_personne('id_personne'));
			$conge->setDate_debut($result->getDate_debut());
			$conge->setDate_fin($result->getDate_fin());
			$conge->setMi_debut_journee($result->getMi_debut_journee());
			$conge->setMi_fin_journee($result->getMi_fin_journee());
			$conge->setNombre_jours($result->getNombre_jours());
			$conge->setAnnee_reference($annee_debut);
			$conge->setId_type_conge('1');
			$conge->setFerme(1);
			 
			if (!in_array($result->getId('id'), $tableau_id_conge))
			{
				$conge->save();
			}
			
			
			//redirection
			$this->_helper->redirector('index');
		}
		
		else
		{
			$this->view->form = 'Impossible valider: id missing !';
		}
	}
	
	public function refuserAction()
	{
		//r�cup�re les param�tres de la requ�te
		$params = $this->getRequest()->getParams();
		//v�rifie que le param�tre id existe
		if(isset($params['id']))
		{
			$id = $params['id'];

			//cr�ation du mod�le pour le refus
			$ferie = new Default_Model_Ferie();
						
			$result = $ferie->find($id);
			$result->setEtat("KO")->save();
			//redirection
			$this->_helper->redirector('index');
		}
		
		else
		{
			$this->view->form = 'Impossible valider: id missing !';
		}
	}
	
	public function afficheradminAction ()
	{
		$ferie = new Default_Model_Ferie;
		$paginator = Zend_Paginator::factory($ferie->fetchAll('Etat = "NC"'));
		$paginator->setItemCountPerPage(10);
		//r�cup�re le num�ro de la page � afficher
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		//on initialise la valeur FerieArray de la vue
		$this->view->ferieArray = $paginator;
	}
	
	public function affichercsmAction ()
	{
		$ferie = new Default_Model_Ferie;
		$paginator = Zend_Paginator::factory($ferie->fetchAll($str = array()));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$this->view->ferieArray = $paginator;
     		
	}
	public function rederigerversindexAction ()
	{
	   $this->_helper->redirector('index');
		
	}
	public function messageAction()
	{
		/*
		 * affiche du messagebox
		 */	
	}

}
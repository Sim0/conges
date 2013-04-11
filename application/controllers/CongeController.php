<?php
class CongeController extends Zend_Controller_Action
{
	//action par d�faut
	public function indexAction()
	{
		//cr�ation d'un d'une instance Default_Model_Users
		$conge = new Default_Model_Conge();

		//$this->view permet d'acc�der � la vue qui sera utilis�e par l'action
		//on initialise la valeur usersArray de la vue
		//(cf. application/views/scripts/users/index.phtml)
		//la valeur correspond � un tableau d'objets de type Default_Model_Users r�cup�r�s par la m�thode fetchAll()
		//$this->view->usersArray = $users->fetchAll();

		//cr�ation de notre objet Paginator avec comme param�tre la m�thode
		//r�cup�rant toutes les entr�es dans notre base de donn�es
		$paginator = Zend_Paginator::factory($conge->fetchAll($str =array()));
		//indique le nombre d�l�ments � afficher par page
		$paginator->setItemCountPerPage(20);
		//r�cup�re le num�ro de la page � afficher
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));

		//$this->view permet d'acc�der � la vue qui sera utilis�e par l'action
		//on initialise la valeur usersArray de la vue
		//(cf. application/views/scripts/users/index.phtml)
		
		
		$this->view->congeArray = $paginator;
		
	}

	public function createAction()
	{
		
		
		//cr�ation du fomulaire
		$form = new Default_Form_Conge();
		//indique l'action qui va traiter le formulaire
		$form->setAction($this->view->url(array('controller' => 'conge', 'action' => 'create'), 'default', true));
		$form->submit->setLabel('Valider');
		$this->view->form = $form;
		$this->view->title = "Deposer un conge";
		$this->view->headLink()->appendStylesheet($this->view->baseUrl().'/css/page2.css');
		$data = array();


		
		if($this->_request->isPost())
		{
				//r�cup�ration des donn�es envoy�es par le formulaire
				$data = $this->_request->getPost();
	
				//v�rifie que les donn�es r�pondent aux conditions des validateurs
			if($form->isValid($data))
			{
					//cr�ation et initialisation d'un objet Default_Model_Users
					//qui sera enregistr� dans la base de donn�es
					$conge = new Default_Model_Conge();
					$conge->setId_personne($form->getValue('id_personne'));
					$conge->setId_type_conge($form->getValue('id_type_conge'));
					$conge->setDate_debut($form->getValue('date_debut'),'yy-mm-dd');
					$conge->setDate_fin($form->getValue('date_fin'),'yy-mm-dd');
					$conge->setMi_debut_journee($form->getValue('mi_debut_journee'));
					$conge->setMi_fin_journee($form->getValue('mi_fin_journee'));
					$conge->setNombre_jours();
					$conge->setAnnee_reference($form->getValue('annee_reference'));
					$conge->setFerme($form->getValue('ferme'));
					/*
					 * Gestion du chevauchement
					 * on appelle le helper pour verifierl'existance des proposition avant 
					 * l'enregistrement dans la base
					 */
					
					if($this->_helper->validation->verifierConges($form->getValue('id_personne'),$form->getValue('date_debut'),$form->getValue('date_fin'),$form->getValue('mi_debut_journee'),$form->getValue('mi_fin_journee'),$form->getValue('id_type_conge'),0))
					{
						$conge->save();
						//redirection
						$this->_helper->redirector('index');
					}
					else 
					{
						$form->populate($data);
						if($form->getValue('annee_reference')==0)
						echo "<strong><em><span style='background-color:rgb(255,0,0)'> annee de reference doit etre non nulle</span></em></strong>";
						else echo "<strong><em><span style='background-color:rgb(255,0,0)'>  conge deja demande</span></em></strong>";
					}
			}
			else 
			{
				$form->populate($data);
				if($form->getValue('annee_reference')==0)
				echo "<strong><em><span style='background-color:rgb(255,0,0)'> annee de reference doit etre non nulle</span></em></strong>";
				else 
				{
					echo "<strong><em><span style='background-color:rgb(255,0,0)'>  conge deja demande</span></em></strong>";
				}
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
		$form = new Default_Form_Conge();
		//indique l'action qui va traiter le formulaire
		$form->setAction($this->view->url(array('controller' => 'conge', 'action' => 'edit'), 'default', true));
		$form->submit->setLabel('Modifier');
		$this->view->title = "Modification du conge";
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
				
				//cr�ation et initialisation d'un objet Default_Model_Conge
				//qui sera enregistr� dans la base de donn�es
				$conge = new Default_Model_Conge();
				$conge->setId($form->getValue('id'));
				$conge->setId_personne($form->getValue('id_personne'));
				
				//$date_debut = new Zend_Date;
				//$date_debut->set($form->getValue('date_debut'),'yy-mm-dd');
				$conge->setDate_debut($form->getValue('date_debut'),'yy-mm-dd');
				$conge->setDate_fin($form->getValue('date_fin'),'yy-mm-dd');
				$conge->setMi_debut_journee($form->getValue('mi_debut_journee'));
				$conge->setMi_fin_journee($form->getValue('mi_fin_journee'));
				$conge->setNombre_jours();
				$conge->setId_type_conge($form->getValue('id_type_conge'));
				$conge->setAnnee_reference($form->getValue('annee_reference'));
				$conge->setFerme($form->getValue('ferme'));
				if($this->_helper->validation->verifierConges($form->getValue('id_personne'),$form->getValue('date_debut'),$form->getValue('date_fin'),$form->getValue('mi_debut_journee'),$form->getValue('mi_fin_journee')))
				{
					$proposition->save();
					//redirection
					$this->_helper->redirector('index');
					
				}
				else 
				{
					$form->populate($data);
					$this->view->title = "<strong><em><span style='background-color:rgb(255,0,0)'> proposition ou conge deja demande</span></em></strong>";
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
				$conge = new Default_Model_Conge();
				$conge = $conge->find($id);

				//assignation des valeurs de l'entr�e dans un tableau
				//tableau utilis� pour la m�thode populate() qui va remplir le champs du formulaire
				//avec les valeurs du tableau
				$data[] = array();
				$data['id'] = $conge->getId();
				$data['id_personne'] = $conge->getId_personne();
				$data['date_debut'] = $conge->getDate_debut();
				$data['mi_debut_journee'] = $conge->getMi_debut_journee();
				$data['date_fin'] = $conge->getDate_fin();
				$data['mi_fin_journee'] = $conge->getMi_fin_journee();
				$data['nombre_jours'] = $conge->getNombre_jours();
				$data['id_type_conge'] = $conge->getId_type_conge();
				$data['annee_reference'] = $conge->getAnnee_reference();
				$data['ferme'] = $conge->getFerme();
				$personne = new Default_Model_Personne();
				$result_set_personnes = $personne->find($id);
				$this->view->title = "Modification de la proposition de Mr/Mme : ".$result_set_personnes->getNom()." ".$result_set_personnes->getPrenom();
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
			$conge = new Default_Model_Conge();
			//appel de la fcontion de suppression avec en argument,
			//la clause where qui sera appliqu�e
			$result = $conge->delete("id=$id");

			//redirection
			$this->_helper->redirector('index');
		}
		else
		{
			$this->view->form = 'Impossible delete: id missing !';
		}
	}
	public function rederigerversindexAction ()
	{
	   $this->_helper->redirector('index');
		
	}

	
}